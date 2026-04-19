<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of reports for current user
     */
    public function index()
    {
        $reports = Report::byReporter(Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new report
     */
    public function create(Request $request)
    {
        $type = $request->query('type', 'cook');
        $userId = $request->query('user_id');
        $dishId = $request->query('dish_id');
        $orderId = $request->query('order_id');

        // Verify the referenced entities exist
        if ($userId && !User::find($userId)) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé');
        }
        if ($dishId && !Dish::find($dishId)) {
            return redirect()->back()->with('error', 'Plat non trouvé');
        }
        if ($orderId && !Order::find($orderId)) {
            return redirect()->back()->with('error', 'Commande non trouvée');
        }

        // Get categories based on type
        $categories = $this->getCategoriesByType($type);

        return view('reports.create', compact('type', 'userId', 'dishId', 'orderId', 'categories'));
    }

    /**
     * Store a newly created report
     */
    public function store(Request $request)
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'type' => 'required|in:cook,client,dish,order,platform',
            'category' => 'required|string',
            'description' => 'required|string|min:10|max:1000',
            'reported_user_id' => 'nullable|exists:users,id',
            'reported_dish_id' => 'nullable|exists:dishes,id',
            'reported_order_id' => 'nullable|exists:orders,id',
        ]);

        // Ensure at least one entity is reported
        if (!$validated['reported_user_id'] && !$validated['reported_dish_id'] && !$validated['reported_order_id']) {
            if ($validated['type'] === 'cook' || $validated['type'] === 'client') {
                return redirect()->back()->with('error', 'Un utilisateur doit être signalé')->withInput();
            }
        }

        // Prevent self-reporting
        if ($validated['reported_user_id'] === Auth::id()) {
            return redirect()->back()->with('error', 'Vous ne pouvez pas vous signaler vous-même')->withInput();
        }

        // Create the report
        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reported_user_id' => $validated['reported_user_id'],
            'reported_dish_id' => $validated['reported_dish_id'],
            'reported_order_id' => $validated['reported_order_id'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'priority' => $this->calculatePriority($validated['category']),
        ]);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Votre signalement a été créé avec succès. Nous l\'examinerons bientôt.');
    }

    /**
     * Display the specified report
     */
    public function show(Report $report)
    {
        $this->authorize('view', $report);

        return view('reports.show', compact('report'));
    }

    /**
     * Admin: Show all reports with filters
     */
    public function adminIndex(Request $request)
    {
        $this->authorize('viewAny', Report::class);

        $query = Report::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        } else {
            // Default: show unresolved
            $query->unresolved();
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                  ->orWhereHas('reporter', fn ($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('reportedUser', fn ($q) => $q->where('name', 'like', "%$search%"));
            });
        }

        $reports = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => Report::pending()->count(),
            'investigating' => Report::investigating()->count(),
            'high_priority' => Report::highPriority()->count(),
            'total_unresolved' => Report::unresolved()->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    /**
     * Admin: Show report details
     */
    public function adminShow(Report $report)
    {
        $this->authorize('view', $report);

        return view('admin.reports.show', compact('report'));
    }

    /**
     * Admin: Update report status
     */
    public function adminUpdate(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $validated = $request->validate([
            'status' => 'required|in:pending,investigating,resolved,rejected,action_taken',
            'admin_comment' => 'nullable|string|max:1000',
        ]);

        $admin_id = Auth::id();
        $comment = $validated['admin_comment'] ?? null;

        switch ($validated['status']) {
            case 'resolved':
                $report->markAsResolved($admin_id, $comment);
                break;
            case 'rejected':
                $report->markAsRejected($admin_id, $comment);
                break;
            case 'action_taken':
                $report->takeAction($admin_id, $comment);
                break;
            default:
                $report->update(['status' => $validated['status']]);
        }

        return redirect()->route('admin.reports.show', $report)
            ->with('success', 'Le signalement a été mis à jour');
    }

    /**
     * Admin: Update priority
     */
    public function updatePriority(Request $request, Report $report)
    {
        $this->authorize('update', $report);

        $validated = $request->validate([
            'priority' => 'required|in:1,2,3,4',
        ]);

        $report->update(['priority' => $validated['priority']]);

        return redirect()->back()->with('success', 'Priorité mise à jour');
    }

    /**
     * Get categories by report type
     */
    private function getCategoriesByType($type)
    {
        return match ($type) {
            'cook', 'client' => [
                'behavior' => 'Comportement inapproprié',
                'abusive' => 'Langage abusif',
                'fraud' => 'Fraude',
            ],
            'dish' => [
                'quality' => 'Qualité insuffisante',
                'expired' => 'Produit expiré',
                'hygiene' => 'Hygiène douteuse',
            ],
            'order' => [
                'quality_issue' => 'Problème de qualité',
                'missing_items' => 'Éléments manquants',
                'late' => 'Livraison tardive',
            ],
            'platform' => [
                'other' => 'Autre',
            ],
            default => [],
        };
    }

    /**
     * Calculate priority based on category
     */
    private function calculatePriority($category)
    {
        return match ($category) {
            'fraud', 'abusive', 'expired' => 4, // critical
            'behavior', 'hygiene', 'quality_issue' => 3, // high
            'missing_items', 'late', 'quality' => 2, // medium
            default => 1, // low
        };
    }

    /**
     * Delete report (reporter only)
     */
    public function destroy(Report $report)
    {
        $this->authorize('delete', $report);

        $report->delete();

        return redirect()->route('reports.index')
            ->with('success', 'Le signalement a été supprimé');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Models\Dish;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class DishController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of dishes.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isClient()) {
            // Clients see all available active dishes from verified (approved) cooks
            $dishes = Dish::with('cook')
                ->whereHas('cook', function ($query) {
                    $query->where('is_verified', true);
                })
                ->where('is_active', true)
                ->orderBy('served_date', 'desc')
                ->get();
            
            // Get verified cooks who have active dishes
            $cooks = User::where('is_verified', true)
                ->whereHas('dishes', function ($query) {
                    $query->where('is_active', true);
                })
                ->with(['dishes' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->get();
            
            return view('dishes.index-client', compact('dishes', 'cooks'));
        } elseif ($user->isCook()) {
            // Cooks see their own dishes
            $dishes = $user->dishes()->orderBy('served_date', 'asc')->get();
            return view('dishes.index', compact('dishes'));
        } else {
            // Admin sees all dishes with cook relation
            $dishes = Dish::with('cook')->get();
            return view('dishes.index', compact('dishes'));
        }
    }

    /**
     * Show the form for creating a new dish.
     */
    public function create()
    {
        $this->authorize('create', Dish::class);
        return view('dishes.create');
    }

    /**
     * Store a newly created dish in storage.
     */
    public function store(StoreDishRequest $request)
    {
        $this->authorize('create', Dish::class);

        $data = $request->validated();
        $data['cook_id'] = auth()->id();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('dishes', 'public');
        }

        $dish = Dish::create($data);

        return redirect()->route('dishes.index')
            ->with('success', 'Plat créé avec succès.');
    }

    /**
     * Display the specified dish.
     */
    public function show(Dish $dish)
    {
        $this->authorize('view', $dish);
        return view('dishes.show', compact('dish'));
    }

    /**
     * Show the form for editing the specified dish.
     */
    public function edit(Dish $dish)
    {
        $this->authorize('update', $dish);
        return view('dishes.edit', compact('dish'));
    }

    /**
     * Update the specified dish in storage.
     */
    public function update(UpdateDishRequest $request, Dish $dish)
    {
        $this->authorize('update', $dish);

        $data = $request->validated();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($dish->photo_path) {
                \Storage::disk('public')->delete($dish->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('dishes', 'public');
        }

        $dish->update($data);

        return redirect()->route('dishes.show', $dish)
            ->with('success', 'Plat modifié avec succès.');
    }

    /**
     * Remove the specified dish from storage.
     */
    public function destroy(Dish $dish)
    {
        $this->authorize('delete', $dish);

        // Delete photo if exists
        if ($dish->photo_path) {
            \Storage::disk('public')->delete($dish->photo_path);
        }

        $dish->delete();

        return redirect()->route('dishes.index')
            ->with('success', 'Plat supprimé avec succès.');
    }

    /**
     * Close service for today - deactivate all dishes for this cook on today
     */
    public function closeService(Request $request)
    {
        // Only cooks can close service
        if (!auth()->user()->isCook()) {
            abort(403, 'Unauthorized');
        }

        $count = auth()->user()->dishes()
            ->whereDate('served_date', today())
            ->update(['is_active' => false]);

        return redirect()->route('dishes.index')
            ->with('success', "Service clôturé. {$count} plat(s) désactivé(s).");
    }

    /**
     * API: Get chef's dishes for real-time updates
     */
    public function apiMyDishes()
    {
        $cook = auth()->user();
        
        if (!$cook->isCook()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $dishes = $cook->dishes()
            ->today()
            ->get()
            ->map(function ($dish) {
                return [
                    'id' => $dish->id,
                    'name' => $dish->name,
                    'price' => $dish->price,
                    'available_qty' => $dish->available_qty,
                    'is_active' => $dish->is_active,
                ];
            });

        return response()->json([
            'dishes' => $dishes,
            'total_active' => $cook->dishes()->today()->where('is_active', true)->count(),
            'total_available' => $cook->dishes()->today()->sum('available_qty'),
        ]);
    }

    /**
     * API: Get today's available dishes for clients
     */
    public function apiTodayDishes()
    {
        $dishes = Dish::today()
            ->active()
            ->with('cook')
            ->get()
            ->map(function ($dish) {
                return [
                    'id' => $dish->id,
                    'name' => $dish->name,
                    'price' => $dish->price,
                    'available_qty' => $dish->available_qty,
                    'cook_name' => $dish->cook->name,
                ];
            });

        $cooks = \App\Models\User::whereHas('dishes', function ($query) {
            $query->today()->active();
        })->count();

        return response()->json([
            'dishes' => $dishes,
            'total_dishes' => $dishes->count(),
            'total_cooks' => $cooks,
            'total_stock' => Dish::today()->active()->sum('available_qty'),
        ]);
    }
}

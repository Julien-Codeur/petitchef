<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderDish;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isClient()) {
            // Clients see their own orders with eager loading
            $orders = $user->ordersAsClient()
                ->with(['cook', 'items.dish'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->isCook()) {
            // Cooks see orders received with eager loading
            $orders = $user->ordersAsCook()
                ->with(['client', 'items.dish'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Admin sees all orders with eager loading
            $orders = Order::with(['client', 'cook', 'items.dish'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order (checkout page).
     */
    public function create()
    {
        $this->authorize('create', Order::class);
        
        // Get cart from session
        $cart = session()->get('cart', []);
        $dishes = Dish::today()->active()->get();

        return view('orders.create', compact('cart', 'dishes'));
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);

        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $items = $data['items'];
                $totalPrice = 0;
                $cookId = null;
                $dishesToUpdate = [];

                // Validate stock and calculate total (using pessimistic locking)
                foreach ($items as $item) {
                    // Lock the row for this transaction
                    $dish = Dish::lockForUpdate()->findOrFail($item['dish_id']);
                    
                    // Check if dish is from same cook
                    if ($cookId === null) {
                        $cookId = $dish->cook_id;
                    } elseif ($cookId !== $dish->cook_id) {
                        throw new \Exception('Vous ne pouvez commander que chez un seul cuisinier à la fois.');
                    }

                    // Check stock with lock held
                    if ($dish->available_qty < $item['quantity']) {
                        throw new \Exception("Stock insuffisant pour {$dish->name}");
                    }

                    $totalPrice += $dish->price * $item['quantity'];
                    $dishesToUpdate[] = [
                        'dish' => $dish,
                        'quantity' => $item['quantity'],
                        'price' => $dish->price,
                    ];
                }

                // Check if cook has reached 15 orders today limit
                $todayOrdersCount = Order::where('cook_id', $cookId)
                    ->whereDate('created_at', today())
                    ->count();

                if ($todayOrdersCount >= 15) {
                    throw new \Exception('Le cuisinier a atteint le maximum de 15 commandes pour aujourd\'hui. Veuillez réessayer demain.');
                }

                // Create order
                $order = Order::create([
                    'client_id' => auth()->id(),
                    'cook_id' => $cookId,
                    'total_price' => $totalPrice,
                    'pickup_time' => $data['pickup_time'],
                    'status' => 'received',
                    'note_client' => $data['note_client'] ?? null,
                ]);

                // Create order items and decrease stock (dishes already locked)
                foreach ($dishesToUpdate as $update) {
                    $dish = $update['dish'];

                    OrderDish::create([
                        'order_id' => $order->id,
                        'dish_id' => $dish->id,
                        'quantity' => $update['quantity'],
                        'unit_price' => $update['price'],
                    ]);

                    // Decrease stock
                    $dish->decreaseStock($update['quantity']);
                }

                // Clear cart
                session()->forget('cart');

                return redirect()->route('orders.show', $order)
                    ->with('success', 'Commande passée avec succès!');
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load('items.dish', 'client', 'cook');
        
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order (update note).
     */
    public function edit(Order $order)
    {
        $this->authorize('update', $order);
        
        if (auth()->user()->isClient() && $order->status !== 'received') {
            abort(403, 'Vous ne pouvez pas modifier une commande en cours de préparation.');
        }

        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        if (auth()->user()->isClient()) {
            // Client can only update note
            $request->validate(['note_client' => 'nullable|string|max:500']);
            $order->update($request->only('note_client'));
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Note mise à jour.');
        }

        if (auth()->user()->isCook()) {
            // Cook can change status
            $request->validate(['status' => 'required|string|in:received,preparing,ready,delivered,cancelled']);
            
            $newStatus = $request->input('status');
            
            if (!$order->canTransition($newStatus)) {
                return back()->with('error', 'Transition de statut invalide.');
            }

            $order->update(['status' => $newStatus]);
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Statut mis à jour.');
        }

        abort(403);
    }

    /**
     * Remove the specified order from storage (cancel).
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        if ($order->status !== 'received') {
            return back()->with('error', 'Seule une commande reçue peut être annulée.');
        }

        // Restore stock
        foreach ($order->items as $item) {
            $item->dish->increment('available_qty', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('orders.index')
            ->with('success', 'Commande annulée.');
    }

    /**
     * Change the status of an order (cook action)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('changeStatus', $order);

        $request->validate(['status' => 'required|string|in:received,preparing,ready,delivered,cancelled']);
        
        $newStatus = $request->input('status');
        
        if (!$order->canTransition($newStatus)) {
            return back()->with('error', 'Transition de statut invalide.');
        }

        $order->update(['status' => $newStatus]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Statut de la commande mis à jour.');
    }

    /**
     * API: Get chef's orders for polling/real-time updates
     */
    public function apiChefOrders()
    {
        $cook = auth()->user();
        
        if (!$cook->isCook()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orders = $cook->ordersAsCook()
            ->with(['client', 'items.dish'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'client_name' => $order->client->name,
                    'total_price' => $order->total_price,
                    'created_at' => $order->created_at->diffForHumans(),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'dish_name' => $item->dish->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                        ];
                    })->toArray(),
                ];
            });

        return response()->json([
            'orders' => $orders,
            'total' => $orders->count(),
            'by_status' => [
                'received' => $cook->ordersAsCook()->where('status', 'received')->count(),
                'preparing' => $cook->ordersAsCook()->where('status', 'preparing')->count(),
                'ready' => $cook->ordersAsCook()->where('status', 'ready')->count(),
            ]
        ]);
    }

    /**
     * API: Get client's orders for polling/real-time updates
     */
    public function apiClientOrders()
    {
        $client = auth()->user();
        
        if (!$client->isClient()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $orders = $client->ordersAsClient()
            ->with(['cook', 'items.dish'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->status,
                    'cook_name' => $order->cook->name,
                    'total_price' => $order->total_price,
                    'created_at' => $order->created_at->diffForHumans(),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'dish_name' => $item->dish->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                        ];
                    })->toArray(),
                ];
            });

        return response()->json([
            'orders' => $orders,
            'total' => $orders->count(),
            'by_status' => [
                'received' => $client->ordersAsClient()->where('status', 'received')->count(),
                'in_preparation' => $client->ordersAsClient()->where('status', 'preparing')->count(),
                'ready' => $client->ordersAsClient()->where('status', 'ready')->count(),
                'delivered' => $client->ordersAsClient()->where('status', 'delivered')->count(),
            ]
        ]);
    }
}

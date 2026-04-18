<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $dishes = Dish::whereIn('id', array_keys($cart))->get();

        return view('cart.index', compact('cart', 'dishes'));
    }

    /**
     * Add a dish to the cart.
     */
    public function add(Request $request, Dish $dish)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check if dish is available
        if (!$dish->isAvailableToday()) {
            return back()->with('error', 'Ce plat n\'est pas disponible.');
        }

        $quantity = $request->input('quantity');

        // Check stock
        if ($dish->available_qty < $quantity) {
            return back()->with('error', 'Stock insuffisant.');
        }

        // Get or initialize cart
        $cart = session()->get('cart', []);

        // Add or update item in cart
        if (isset($cart[$dish->id])) {
            $cart[$dish->id] += $quantity;
        } else {
            $cart[$dish->id] = $quantity;
        }

        // Verify total quantity doesn't exceed available
        if ($cart[$dish->id] > $dish->available_qty) {
            $cart[$dish->id] = $dish->available_qty;
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Plat ajouté au panier.');
    }

    /**
     * Remove a dish from the cart.
     */
    public function remove(Dish $dish)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$dish->id])) {
            unset($cart[$dish->id]);
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Plat retiré du panier.');
    }

    /**
     * Update quantity in cart.
     */
    public function update(Request $request, Dish $dish)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0|max:' . $dish->available_qty,
        ]);

        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity');

        if ($quantity === 0 || $quantity === '0') {
            unset($cart[$dish->id]);
        } else {
            $cart[$dish->id] = $quantity;
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Panier mis à jour.');
    }

    /**
     * Clear the cart.
     */
    public function clear()
    {
        session()->forget('cart');

        return redirect()->route('dishes.index')
            ->with('success', 'Panier vidé.');
    }
}

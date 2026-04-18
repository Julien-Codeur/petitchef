<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Passer une commande') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ $message }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Commande Items --}}
                <div class="lg:col-span-2 bg-white overflow-hidden shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h3>

                    @php
                        $total = 0;
                        $cookId = null;
                    @endphp

                    @if(count($cart) > 0 && count($dishes) > 0)
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Plat</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dishes as $dish)
                                    @if(isset($cart[$dish->id]))
                                        @php
                                            $itemTotal = $cart[$dish->id] * $dish->price;
                                            $total += $itemTotal;
                                            if ($cookId === null) $cookId = $dish->cook_id;
                                        @endphp
                                        <tr class="border-b">
                                            <td class="px-4 py-2">{{ $dish->name }}</td>
                                            <td class="px-4 py-2">{{ number_format($dish->price, 2) }}€</td>
                                            <td class="px-4 py-2">{{ $cart[$dish->id] }}</td>
                                            <td class="px-4 py-2 font-semibold">{{ number_format($itemTotal, 2) }}€</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4 pt-4 border-t text-right">
                            <p class="text-2xl font-bold text-blue-600">Total: {{ number_format($total, 2) }}€</p>
                        </div>
                    @else
                        <p class="text-gray-600">Votre panier est vide.</p>
                    @endif
                </div>

                {{-- Formulaire Commande --}}
                <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Finaliser</h3>

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(count($cart) > 0)
                        <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                            @csrf

                            <div class="mb-4">
                                <label for="pickup_time" class="block text-gray-700 text-sm font-bold mb-2">Heure de retrait:</label>
                                <input type="time" name="pickup_time" id="pickup_time" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline">
                            </div>

                            <div class="mb-4">
                                <label for="note_client" class="block text-gray-700 text-sm font-bold mb-2">Note (optionnel):</label>
                                <textarea name="note_client" id="note_client" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 focus:outline-none focus:shadow-outline" placeholder="Allergies, preferences, etc."></textarea>
                            </div>

                            <div class="mb-4 p-3 bg-gray-50 rounded">
                                <p class="text-sm text-gray-600 mb-2"><strong>Cuisinier:</strong></p>
                                @php
                                    $cartDishId = array_key_first($cart);
                                    $cookForOrder = $dishes->find($cartDishId);
                                @endphp
                                <p class="font-semibold">{{ $cookForOrder?->cook->name ?? 'Chargement...' }}</p>
                            </div>

                            <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mb-2">
                                Passer la commande
                            </button>

                            <a href="{{ route('cart.index') }}" class="block w-full text-center bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Modifier le panier
                            </a>
                        </form>
                    @else
                        <div class="text-center">
                            <p class="text-gray-600 mb-4">Votre panier est vide.</p>
                            <a href="{{ route('dishes.index') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Voir les plats
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add items as hidden inputs in form
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('orderForm');
            if (form) {
                const cartData = {!! json_encode($cart) !!};
                let index = 0;
                for (const [dishId, quantity] of Object.entries(cartData)) {
                    const dishIdInput = document.createElement('input');
                    dishIdInput.type = 'hidden';
                    dishIdInput.name = `items[${index}][dish_id]`;
                    dishIdInput.value = dishId;
                    form.appendChild(dishIdInput);

                    const qtyInput = document.createElement('input');
                    qtyInput.type = 'hidden';
                    qtyInput.name = `items[${index}][quantity]`;
                    qtyInput.value = quantity;
                    form.appendChild(qtyInput);

                    index++;
                }
            }
        });
    </script>
</x-app-layout>

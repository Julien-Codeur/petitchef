<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($message = Session::get('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ $message }}
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ $message }}
                </div>
            @endif

            @if(count($cart) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Panier Items --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach($dishes as $dish)
                                        @if(isset($cart[$dish->id]))
                                            @php
                                                $itemTotal = $cart[$dish->id] * $dish->price;
                                                $total += $itemTotal;
                                            @endphp
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('dishes.show', $dish) }}" class="text-blue-500 hover:text-blue-700">
                                                        {{ $dish->name }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($dish->price, 2) }}€</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <form action="{{ route('cart.update', $dish) }}" method="POST" class="flex gap-2">
                                                        @csrf @method('PATCH')
                                                        <input type="number" name="quantity" value="{{ $cart[$dish->id] }}" min="0" max="{{ $dish->available_qty }}" class="w-20 px-2 py-1 border rounded" onchange="this.form.submit()">
                                                    </form>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ number_format($itemTotal, 2) }}€</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <form action="{{ route('cart.remove', $dish) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Supprimer</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Résumé du panier --}}
                    <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé</h3>
                        
                        <div class="space-y-2 border-b pb-4 mb-4">
                            <div class="flex justify-between">
                                <span>Total:</span>
                                <span class="font-bold text-2xl text-blue-600">{{ number_format($total, 2) }}€</span>
                            </div>
                        </div>

                        <a href="{{ route('orders.create') }}" class="w-full block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center mb-2">
                            Procéder à la commande
                        </a>

                        <a href="{{ route('dishes.index') }}" class="w-full block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center mb-2">
                            Continuer vos achats
                        </a>

                        <form action="{{ route('cart.clear') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Vider le panier?')">
                                Vider le panier
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow rounded-lg p-6 text-center">
                    <p class="text-gray-600 mb-4">Votre panier est vide.</p>
                    <a href="{{ route('dishes.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Voir les plats
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

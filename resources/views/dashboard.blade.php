<x-app-layout>
    <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                @if(auth()->user()->isCook())
                    👨‍🍳 Dashboard Cuisinier
                @elseif(auth()->user()->isClient())
                    🛒 Bienvenue sur PetitChef
                @else
                    👮 Dashboard Admin
                @endif
            </h1>
            <p class="text-gray-600 mt-2">Bienvenue, {{ auth()->user()->name }}!</p>
        </div>

        <!-- CUISINIER DASHBOARD -->
        @if(auth()->user()->isCook())
            <div class="space-y-6">
                <!-- Mes plats du jour -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gradient-to-r from-orange-50 to-red-50 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">📍 Mes plats du jour</h2>
                            <a href="{{ route('dishes.create') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                ➕ Ajouter un plat
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $myDishes = auth()->user()->dishes()->today()->get();
                        @endphp
                        
                        @if($myDishes->count())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($myDishes as $dish)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h3 class="font-semibold text-lg">{{ $dish->name }}</h3>
                                                <p class="text-gray-600 text-sm">{{ $dish->description }}</p>
                                            </div>
                                            <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">{{ $dish->available_qty }} restants</span>
                                        </div>
                                        <div class="flex justify-between items-center mt-4">
                                            <span class="text-xl font-bold text-green-600">{{ number_format($dish->price, 2) }}€</span>
                                            <div class="space-x-2">
                                                <a href="{{ route('dishes.edit', $dish) }}" class="inline-block px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">✏️ Modifier</a>
                                                <form method="POST" action="{{ route('dishes.destroy', $dish) }}" style="display:inline;">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="inline-block px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600" onclick="return confirm('Supprimer?')">🗑️ Supprimer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Aucun plat pour aujourd'hui</p>
                        @endif
                    </div>
                </div>

                <!-- Commandes reçues -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">📦 Commandes reçues</h2>
                    </div>
                    <div class="p-6">
                        @php
                            $orders = auth()->user()->ordersAsCook()->latest()->get();
                        @endphp

                        @if($orders->count())
                            <div class="space-y-3">
                                @foreach($orders as $order)
                                    <div class="border rounded-lg p-4 flex justify-between items-center hover:bg-gray-50">
                                        <div>
                                            <p class="font-semibold">Commande #{{ $order->id }}</p>
                                            <p class="text-sm text-gray-600">De: {{ $order->client->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                @foreach($order->items as $item)
                                                    {{ $item->quantity }}x {{ $item->dish->name }}{{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-lg">{{ number_format($order->total_price, 2) }}€</p>
                                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                                @if($order->status === 'received') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'preparing') bg-yellow-100 text-yellow-800
                                                @elseif($order->status === 'ready') bg-green-100 text-green-800
                                                @elseif($order->status === 'delivered') bg-purple-100 text-purple-800
                                                @else bg-red-100 text-red-800
                                                @endif
                                            ">
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                            <a href="{{ route('orders.show', $order) }}" class="inline-block mt-2 px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600">Voir</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Aucune commande pour le moment</p>
                        @endif
                    </div>
                </div>
            </div>

        <!-- CLIENT DASHBOARD -->
        @elseif(auth()->user()->isClient())
            <div class="space-y-6">
                <!-- Plats disponibles -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">🍽️ Plats disponibles aujourd'hui</h2>
                            <a href="{{ route('cart.index') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                🛒 Voir mon panier
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @php
                            $dishes = \App\Models\Dish::today()->active()->get();
                        @endphp

                        @if($dishes->count())
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($dishes as $dish)
                                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition">
                                        <div class="bg-gray-200 h-40 flex items-center justify-center text-gray-400">
                                            📸 Photo
                                        </div>
                                        <div class="p-4">
                                            <h3 class="font-semibold text-lg">{{ $dish->name }}</h3>
                                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($dish->description, 50) }}</p>
                                            <div class="flex justify-between items-center mb-3">
                                                <span class="text-xl font-bold text-green-600">{{ number_format($dish->price, 2) }}€</span>
                                                <span class="text-sm text-gray-600">{{ $dish->available_qty }} dispo</span>
                                            </div>
                                            <div class="space-x-2">
                                                <a href="{{ route('dishes.show', $dish) }}" class="inline-block px-3 py-2 bg-blue-500 text-white rounded text-sm hover:bg-blue-600">Détails</a>
                                                <button type="button" class="inline-block px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700" onclick="document.getElementById('cart_dish_{{ $dish->id }}').showModal()">
                                                    ➕ Ajouter
                                                </button>
                                            </div>

                                            <!-- Modal ajouter au panier -->
                                            <dialog id="cart_dish_{{ $dish->id }}" class="modal">
                                                <div class="modal-box">
                                                    <h3 class="font-bold text-lg">{{ $dish->name }}</h3>
                                                    <form action="{{ route('cart.add', $dish) }}" method="POST" class="mt-4">
                                                        @csrf
                                                        <div class="mb-4">
                                                            <label class="block text-sm font-semibold mb-2">Quantité</label>
                                                            <input type="number" name="quantity" min="1" max="{{ $dish->available_qty }}" value="1" class="w-full px-3 py-2 border rounded">
                                                        </div>
                                                        <div class="flex justify-end gap-3">
                                                            <button type="button" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400" onclick="document.getElementById('cart_dish_{{ $dish->id }}').close()">Annuler</button>
                                                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Ajouter</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </dialog>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Aucun plat disponible aujourd'hui</p>
                        @endif
                    </div>
                </div>

                <!-- Mes commandes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">📋 Mes commandes</h2>
                    </div>
                    <div class="p-6">
                        @php
                            $myOrders = auth()->user()->ordersAsClient()->latest()->get();
                        @endphp

                        @if($myOrders->count())
                            <div class="space-y-3">
                                @foreach($myOrders as $order)
                                    <div class="border rounded-lg p-4 flex justify-between items-center hover:bg-gray-50">
                                        <div>
                                            <p class="font-semibold">Commande #{{ $order->id }}</p>
                                            <p class="text-sm text-gray-600">Chez: {{ $order->cook->name }}</p>
                                            <p class="text-sm text-gray-600">Retrait: {{ $order->pickup_time }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-lg">{{ number_format($order->total_price, 2) }}€</p>
                                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                                @if($order->status === 'received') bg-blue-100 text-blue-800
                                                @elseif($order->status === 'preparing') bg-yellow-100 text-yellow-800
                                                @elseif($order->status === 'ready') bg-green-100 text-green-800
                                                @elseif($order->status === 'delivered') bg-purple-100 text-purple-800
                                                @else bg-red-100 text-red-800
                                                @endif
                                            ">
                                                {{ $order->getStatusLabel() }}
                                            </span>
                                            <a href="{{ route('orders.show', $order) }}" class="inline-block mt-2 px-3 py-1 bg-gray-500 text-white rounded text-sm hover:bg-gray-600">Voir</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Aucune commande pour le moment</p>
                        @endif
                    </div>
                </div>
            </div>

        <!-- ADMIN DASHBOARD -->
        @else
            <div class="space-y-6">
                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                        <p class="text-gray-600 text-sm">Total Commandes</p>
                        <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Order::count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                        <p class="text-gray-600 text-sm">Total Utilisateurs</p>
                        <p class="text-3xl font-bold text-green-600">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-orange-500">
                        <p class="text-gray-600 text-sm">Cuisiniers Vérifiés</p>
                        <p class="text-3xl font-bold text-orange-600">{{ \App\Models\User::where('role', 'cook')->where('is_verified', true)->count() }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
                        <p class="text-gray-600 text-sm">Plats du jour</p>
                        <p class="text-3xl font-bold text-purple-600">{{ \App\Models\Dish::today()->count() }}</p>
                    </div>
                </div>

                <!-- Toutes les commandes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-pink-50 border-b">
                        <h2 class="text-xl font-semibold text-gray-900">📦 Toutes les commandes</h2>
                    </div>
                    <div class="p-6">
                        @php
                            $allOrders = \App\Models\Order::latest()->limit(10)->get();
                        @endphp

                        @if($allOrders->count())
                            <div class="overflow-x-auto">
                                <table class="min-w-full border-collapse">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="border p-3 text-left">ID</th>
                                            <th class="border p-3 text-left">Client</th>
                                            <th class="border p-3 text-left">Cuisinier</th>
                                            <th class="border p-3 text-left">Prix</th>
                                            <th class="border p-3 text-left">Status</th>
                                            <th class="border p-3 text-left">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allOrders as $order)
                                            <tr class="hover:bg-gray-50">
                                                <td class="border p-3">#{{ $order->id }}</td>
                                                <td class="border p-3">{{ $order->client->name }}</td>
                                                <td class="border p-3">{{ $order->cook->name }}</td>
                                                <td class="border p-3">{{ number_format($order->total_price, 2) }}€</td>
                                                <td class="border p-3">
                                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                                        @if($order->status === 'received') bg-blue-100 text-blue-800
                                                        @elseif($order->status === 'preparing') bg-yellow-100 text-yellow-800
                                                        @elseif($order->status === 'ready') bg-green-100 text-green-800
                                                        @elseif($order->status === 'delivered') bg-purple-100 text-purple-800
                                                        @else bg-red-100 text-red-800
                                                        @endif
                                                    ">
                                                        {{ $order->getStatusLabel() }}
                                                    </span>
                                                </td>
                                                <td class="border p-3">
                                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">Voir</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Aucune commande</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
</x-app-layout>

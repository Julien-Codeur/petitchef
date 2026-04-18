<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $dish->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        @if($dish->photo_path)
                            <img src="{{ asset('storage/' . $dish->photo_path) }}" alt="{{ $dish->name }}" class="w-full h-96 object-cover rounded">
                        @else
                            <div class="w-full h-96 bg-gray-200 flex items-center justify-center rounded">
                                <span class="text-gray-400">Pas de photo</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-6">
                        <p class="text-gray-600 text-sm mb-4">Par <strong>{{ $dish->cook->name }}</strong></p>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $dish->name }}</h3>
                        
                        <p class="text-gray-600 mb-6">{{ $dish->description }}</p>

                        <div class="border-t pt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Prix:</span>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($dish->price, 2) }}€</span>
                            </div>
                            
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Disponible:</span>
                                <span class="font-semibold {{ $dish->available_qty > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $dish->available_qty }} portie(s)
                                </span>
                            </div>

                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600">Date de service:</span>
                                <span class="font-semibold">{{ $dish->served_date->format('d/m/Y') }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Statut:</span>
                                <span class="font-semibold {{ $dish->is_active ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $dish->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>

                        @auth
                            @if(auth()->user()->isCook() && auth()->user()->id === $dish->cook_id)
                                <div class="mt-6 flex gap-2">
                                    <a href="{{ route('dishes.edit', $dish) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center">
                                        Modifier
                                    </a>
                                    <form action="{{ route('dishes.destroy', $dish) }}" method="POST" class="flex-1">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Êtes-vous sûr?')">
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            @elseif(auth()->user()?->isClient() && $dish->available_qty > 0)
                                <button onclick="openAddToCartModal({{ $dish->id }}, '{{ $dish->name }}', {{ $dish->price }}, {{ $dish->available_qty }})" class="w-full mt-6 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Ajouter au panier
                                </button>
                            @endif
                        @endauth

                        <a href="{{ route('dishes.index') }}" class="block w-full mt-2 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Ajouter au panier --}}
    @auth
        @if(auth()->user()->isClient())
            <div id="addToCartModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <h3 class="text-lg font-semibold text-gray-900">Ajouter au panier</h3>
                    <p class="text-gray-600 mt-2"><strong id="dishName"></strong> - <span id="dishPrice"></span>€</p>
                    
                    <form id="addToCartForm" method="POST">
                        @csrf
                        <div class="mt-4">
                            <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantité:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        </div>
                        
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="flex-1 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Ajouter
                            </button>
                            <button type="button" onclick="closeAddToCartModal()" class="flex-1 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openAddToCartModal(dishId, dishName, dishPrice, maxQty) {
                    document.getElementById('dishName').textContent = dishName;
                    document.getElementById('dishPrice').textContent = dishPrice.toFixed(2);
                    document.getElementById('quantity').max = maxQty;
                    document.getElementById('quantity').value = 1;
                    document.getElementById('addToCartForm').action = `/cart/${dishId}`;
                    document.getElementById('addToCartModal').classList.remove('hidden');
                }

                function closeAddToCartModal() {
                    document.getElementById('addToCartModal').classList.add('hidden');
                }
            </script>
        @endif
    @endauth
</x-app-layout>

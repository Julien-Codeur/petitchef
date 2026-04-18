<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Plats') }}
            </h2>
            @auth
                @if(auth()->user()->isCook())
                    <a href="{{ route('dishes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Ajouter un plat
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Messages --}}
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

            {{-- Dishes Grid --}}
            @if($dishes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($dishes as $dish)
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            @if($dish->photo_path)
                                <img src="{{ asset('storage/' . $dish->photo_path) }}" alt="{{ $dish->name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">Pas de photo</span>
                                </div>
                            @endif
                            
                            <div class="px-6 py-4">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $dish->name }}</h3>
                                <p class="text-gray-600 text-sm mt-2">{{ $dish->description }}</p>
                                
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-2xl font-bold text-blue-600">{{ number_format($dish->price, 2) }}€</span>
                                    <span class="text-sm text-gray-500">
                                        @if($dish->available_qty > 0)
                                            {{ $dish->available_qty }} disponible
                                        @else
                                            <span class="text-red-500">Rupture</span>
                                        @endif
                                    </span>
                                </div>

                                <div class="mt-4 text-xs text-gray-500">
                                    Par: <strong>{{ $dish->cook->name }}</strong>
                                </div>

                                <div class="mt-4 flex gap-2">
                                    @if(auth()->user()?->isCook() && auth()->user()->id === $dish->cook_id)
                                        <a href="{{ route('dishes.edit', $dish) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-center">
                                            Modifier
                                        </a>
                                        <form action="{{ route('dishes.destroy', $dish) }}" method="POST" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Êtes-vous sûr?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    @elseif(auth()->user()?->isClient() && $dish->available_qty > 0)
                                        <button onclick="openAddToCartModal({{ $dish->id }}, '{{ $dish->name }}', {{ $dish->price }}, {{ $dish->available_qty }})" class="flex-1 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Ajouter au panier
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow rounded-lg p-6 text-center">
                    <p class="text-gray-600">Aucun plat disponible pour le moment.</p>
                </div>
            @endif
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

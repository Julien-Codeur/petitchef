<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes commandes') }}
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

            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="bg-white overflow-hidden shadow rounded-lg p-6">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Commande #{{ $order->id }}</p>
                                    <p class="text-lg font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Statut</p>
                                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                                        @switch($order->status)
                                            @case('received')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('preparing')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('ready')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('delivered')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('cancelled')
                                                bg-red-100 text-red-800
                                                @break
                                        @endswitch">
                                        {{ $order->getStatusLabel() }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Heure de retrait</p>
                                    <p class="text-lg font-semibold">{{ $order->pickup_time->format('H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Total</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ number_format($order->total_price, 2) }}€</p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <p class="text-gray-600 text-sm mb-2">{{ $orders->count() > 1 ? 'Cuisinier' : 'Client' }}: <strong>{{ auth()->user()->isCook() ? $order->client->name : $order->cook->name }}</strong></p>
                                @if($order->note_client)
                                    <p class="text-gray-600 text-sm">Note: <strong>{{ $order->note_client }}</strong></p>
                                @endif
                            </div>

                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('orders.show', $order) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Détails
                                </a>

                                @if(auth()->user()->isClient() && $order->status === 'received')
                                    <a href="{{ route('orders.edit', $order) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                        Modifier note
                                    </a>
                                    <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Annuler cette commande?')">
                                            Annuler
                                        </button>
                                    </form>
                                @elseif(auth()->user()->isCook())
                                    @if($order->canTransition('preparing') && $order->status === 'received')
                                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="preparing">
                                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                                Commencer préparation
                                            </button>
                                        </form>
                                    @endif
                                    @if($order->canTransition('ready') && $order->status === 'preparing')
                                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="ready">
                                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                Prête pour retrait
                                            </button>
                                        </form>
                                    @endif
                                    @if($order->canTransition('delivered') && $order->status === 'ready')
                                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="delivered">
                                            <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                                Livrée
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white overflow-hidden shadow rounded-lg p-6 text-center">
                    <p class="text-gray-600 mb-4">Aucune commande.</p>
                    @if(auth()->user()->isClient())
                        <a href="{{ route('dishes.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Voir les plats
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

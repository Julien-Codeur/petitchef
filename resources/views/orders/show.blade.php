<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Détails commande #' . $order->id) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Order Info --}}
            <div class="bg-white overflow-hidden shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de la commande</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm">Numéro</p>
                        <p class="text-lg font-semibold">#{{ $order->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Date</p>
                        <p class="text-lg font-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Heure de retrait</p>
                        <p class="text-lg font-semibold">{{ $order->pickup_time->format('H:i') }}</p>
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
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t">
                    <div>
                        <p class="text-gray-600 text-sm">Client</p>
                        <p class="text-lg font-semibold">{{ $order->client->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->client->email }}</p>
                        <p class="text-sm text-gray-500">{{ $order->client->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Cuisinier</p>
                        <p class="text-lg font-semibold">{{ $order->cook->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->cook->email }}</p>
                        <p class="text-sm text-gray-500">{{ $order->cook->phone }}</p>
                    </div>
                </div>

                @if($order->note_client)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-gray-600 text-sm">Note du client</p>
                        <p class="text-gray-800">{{ $order->note_client }}</p>
                    </div>
                @endif
            </div>

            {{-- Order Items --}}
            <div class="bg-white overflow-hidden shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Plats commandés</h3>
                
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $total = 0; @endphp
                        @foreach($order->items as $item)
                            @php $itemTotal = $item->quantity * $item->unit_price; $total += $itemTotal; @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->dish->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->unit_price, 2) }}€</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">{{ number_format($itemTotal, 2) }}€</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 pt-4 border-t text-right">
                    <p class="text-2xl font-bold text-blue-600">Total: {{ number_format($order->total_price, 2) }}€</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 mb-6">
                <a href="{{ route('orders.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Retour aux commandes
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
    </div>
</x-app-layout>

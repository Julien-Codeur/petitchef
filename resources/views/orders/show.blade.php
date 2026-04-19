<x-sidebar-layout>
<div style="padding: 0;">
    <!-- Entête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">Commande #{{ $order->id }}</h1>
        <p style="color: #999; margin: 0;">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
    </div>

    <!-- Affichage du statut et du total -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <p style="margin: 0 0 8px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Statut actuel</p>
                <span style="display: inline-block; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; color: white;
                    @switch($order->status)
                        @case('received')
                            background-color: #2196f3;
                            @break
                        @case('preparing')
                            background-color: #ff9800;
                            @break
                        @case('ready')
                            background-color: #4caf50;
                            @break
                        @case('delivered')
                            background-color: #9c27b0;
                            @break
                        @case('cancelled')
                            background-color: #d32f2f;
                            @break
                    @endswitch">
                    {{ $order->getStatusLabel() }}
                </span>
            </div>
            <div>
                <p style="margin: 0 0 8px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Total</p>
                <p style="margin: 0; font-size: 24px; font-weight: 700; color: #ff6b35;">{{ number_format($order->total_price, 2) }}€</p>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Colonne gauche: Articles et détails -->
        <div>
            <!-- Articles -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">Articles ({{ $order->items->count() }})</h2>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($order->items as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background-color: #f9f9f9; border-radius: 8px; border: 1px solid #e0e0e0;">
                            <div>
                                <p style="margin: 0; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $item->dish->name }}</p>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">{{ $item->quantity }}x {{ number_format($item->unit_price, 2) }}€</p>
                            </div>
                            <p style="margin: 0; font-weight: 700; color: #ff6b35; font-size: 16px;">{{ number_format($item->quantity * $item->unit_price, 2) }}€</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Note client -->
            @if($order->note_client)
                <div style="background-color: #fff3e0; border-radius: 8px; padding: 16px; border-left: 4px solid #ff6b35; margin-bottom: 20px;">
                    <p style="margin: 0 0 8px 0; font-size: 12px; color: #e65100; font-weight: 600; text-transform: uppercase;">📝 Note spéciale du client</p>
                    <p style="margin: 0; font-size: 14px; color: #333;">{{ $order->note_client }}</p>
                </div>
            @endif
        </div>

        <!-- Colonne droite: Informations et actions -->
        <div>
            <!-- Infos commande -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 20px;">
                <h2 style="margin: 0 0 16px 0; font-size: 14px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase;">Informations</h2>
                
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Heure de retrait -->
                    <div>
                        <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Heure de retrait</p>
                        <p style="margin: 0; font-size: 16px; font-weight: 700; color: #333;">{{ $order->pickup_time }}</p>
                    </div>

                    <!-- Cuisinier -->
                    <div>
                        <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Cuisinier</p>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; background-color: #ff6b35; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px;">
                                {{ substr($order->cook->name, 0, 1) }}
                            </div>
                            <p style="margin: 0; font-weight: 700; color: #333;">{{ $order->cook->name }}</p>
                        </div>
                    </div>

                    <!-- Client -->
                    <div>
                        <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Client</p>
                        <p style="margin: 0; font-weight: 700; color: #333;">{{ $order->client->name }}</p>
                    </div>

                    <!-- Date création -->
                    <div>
                        <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600;">Créée le</p>
                        <p style="margin: 0; font-weight: 700; color: #333;">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions selon le rôle -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
                <h2 style="margin: 0 0 16px 0; font-size: 14px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; text-transform: uppercase;">Actions</h2>
                
                @if(auth()->user()->isClient())
                    @if($order->status === 'received')
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width: 100%; background-color: #ffebee; color: #d32f2f; padding: 12px 16px; border-radius: 6px; font-size: 14px; font-weight: 700; border: 1px solid #d32f2f; cursor: pointer;">
                                ✕ Annuler la commande
                            </button>
                        </form>
                    @else
                        <p style="margin: 0; text-align: center; color: #999; font-size: 13px;">
                            Vous ne pouvez pas annuler une commande en cours de préparation.
                        </p>
                    @endif

                @elseif(auth()->user()->isCook() && $order->cook_id === auth()->id())
                    @if($order->status === 'received')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" style="margin-bottom: 12px;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="preparing">
                            <button type="submit" style="width: 100%; background-color: #ff9800; color: white; padding: 12px 16px; border-radius: 6px; font-size: 14px; font-weight: 700; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#f57c00'" onmouseout="this.style.backgroundColor='#ff9800'">
                                🔄 Commencer la préparation
                            </button>
                        </form>
                    @endif

                    @if($order->status === 'preparing')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" style="margin-bottom: 12px;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="ready">
                            <button type="submit" style="width: 100%; background-color: #4caf50; color: white; padding: 12px 16px; border-radius: 6px; font-size: 14px; font-weight: 700; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#388e3c'" onmouseout="this.style.backgroundColor='#4caf50'">
                                ✓ Prête pour retrait
                            </button>
                        </form>
                    @endif

                    @if($order->status === 'ready')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" style="margin-bottom: 12px;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="delivered">
                            <button type="submit" style="width: 100%; background-color: #9c27b0; color: white; padding: 12px 16px; border-radius: 6px; font-size: 14px; font-weight: 700; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#7b1fa2'" onmouseout="this.style.backgroundColor='#9c27b0'">
                                📦 Marquée comme livrée
                            </button>
                        </form>
                    @endif

                    @if(in_array($order->status, ['received', 'preparing', 'ready']))
                        <form action="{{ route('orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" style="width: 100%; background-color: #ffebee; color: #d32f2f; padding: 12px 16px; border-radius: 6px; font-size: 14px; font-weight: 700; border: 1px solid #d32f2f; cursor: pointer;" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande?')">
                                ✕ Annuler la commande
                            </button>
                        </form>
                    @endif
                @endif

                <!-- Lien retour -->
                <div style="margin-top: 16px; text-align: center;">
                    <a href="{{ route('orders.index') }}" style="color: #ff6b35; font-size: 13px; font-weight: 600; text-decoration: none;">← Retour à la liste</a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-sidebar-layout>

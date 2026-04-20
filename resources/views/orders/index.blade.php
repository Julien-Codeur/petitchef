<x-client-sidebar-layout>
<div style="padding: 0;">
    <!-- Entête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">
            @if(auth()->user()->isClient())
                Mes Commandes 📋
            @elseif(auth()->user()->isCook())
                Commandes Reçues 👨‍🍳
            @else
                Toutes les Commandes 📊
            @endif
        </h1>
        <p style="color: #999; margin: 0;">Historique complet de vos transactions</p>
    </div>

    <!-- Messages -->
    @if ($message = Session::get('success'))
        <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #4caf50;">
            <p style="margin: 0;">✓ {{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div style="background-color: #ffebee; border-left: 4px solid #d32f2f; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #d32f2f;">
            <p style="margin: 0;">✗ {{ $message }}</p>
        </div>
    @endif

    <!-- Barre d'action -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 16px; margin-bottom: 20px; display: flex; gap: 12px;">
        @if(auth()->user()->isClient())
            <a href="{{ route('dishes.menu-du-jour') }}" style="background-color: #ff6b35; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                🍽️ Continuer mes courses
            </a>
        @endif
    </div>

    @if($orders->count() > 0)
        <!-- Liste des commandes -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($orders as $order)
                <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
                    <!-- En-tête de commande -->
                    <div style="background-color: #f9f9f9; border-bottom: 1px solid #e0e0e0; padding: 16px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">Commande #{{ $order->id }}</h3>
                            <p style="margin: 0; font-size: 12px; color: #999;">
                                Créée le {{ $order->created_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: inline-block; padding: 6px 16px; border-radius: 20px; font-size: 12px; font-weight: 700; color: white;
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
                            <p style="margin: 8px 0 0 0; font-size: 16px; font-weight: 700; color: #ff6b35;">{{ number_format($order->total_price, 2) }}€</p>
                        </div>
                    </div>

                    <!-- Contenu commande -->
                    <div style="padding: 16px;">
                        <!-- Chef & Pickup Time -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e0e0e0;">
                            <div>
                                <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Cuisinier</p>
                                <p style="margin: 0; font-weight: 700; color: #333;">{{ $order->cook->name }}</p>
                            </div>
                            <div>
                                <p style="margin: 0 0 4px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Heure de Retrait</p>
                                <p style="margin: 0; font-weight: 700; color: #333;">{{ $order->pickup_time }}</p>
                            </div>
                        </div>

                        <!-- Articles -->
                        <div style="margin-bottom: 16px;">
                            <p style="margin: 0 0 12px 0; font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase;">Articles ({{ $order->items->count() }})</p>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                @foreach($order->items as $item)
                                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px; background-color: #f9f9f9; border-radius: 6px;">
                                        <div>
                                            <p style="margin: 0; font-weight: 600; color: #333;">{{ $item->dish->name }}</p>
                                            <p style="margin: 2px 0 0 0; font-size: 12px; color: #999;">{{ $item->quantity }}x {{ number_format($item->unit_price, 2) }}€</p>
                                        </div>
                                        <p style="margin: 0; font-weight: 700; color: #ff6b35;">{{ number_format($item->quantity * $item->unit_price, 2) }}€</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Note client si présente -->
                        @if($order->note_client)
                            <div style="padding: 12px; background-color: #fff3e0; border-radius: 6px; margin-bottom: 16px; border-left: 4px solid #ff6b35;">
                                <p style="margin: 0; font-size: 12px; color: #e65100; font-weight: 600; margin-bottom: 4px;">📝 Note spéciale:</p>
                                <p style="margin: 0; font-size: 13px; color: #333;">{{ $order->note_client }}</p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div style="display: flex; gap: 12px; justify-content: flex-end;">
                            <a href="{{ route('orders.show', $order) }}" style="background-color: #f5f5f5; color: #333; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #e0e0e0;">
                                Détails →
                            </a>

                            @if(auth()->user()->isClient() && $order->status === 'received')
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background-color: #ffebee; color: #d32f2f; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #e0e0e0; cursor: pointer;">
                                        ✕ Annuler
                                    </button>
                                </form>
                            @endif

                            @if(auth()->user()->isCook() && in_array($order->status, ['received', 'preparing', 'ready']))
                                <a href="{{ route('orders.show', $order) }}" style="background-color: #ff6b35; color: white; padding: 8px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #ff6b35;">
                                    ⚙️ Gérer →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- État vide -->
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 60px; text-align: center;">
            <p style="font-size: 48px; margin: 0 0 16px 0;">
                @if(auth()->user()->isClient())
                    📭
                @elseif(auth()->user()->isCook())
                    🍳
                @else
                    📊
                @endif
            </p>
            <p style="font-size: 18px; color: #333; margin: 0 0 8px 0; font-weight: 700;">
                @if(auth()->user()->isClient())
                    Aucune commande pour le moment
                @elseif(auth()->user()->isCook())
                    Aucune commande reçue
                @else
                    Aucune commande
                @endif
            </p>
            <p style="font-size: 14px; color: #999; margin: 0 0 24px 0;">
                @if(auth()->user()->isClient())
                    Commencez à explorer le menu du jour pour passer votre première commande !
                @else
                    Vous recevrez les commandes des clients ici
                @endif
            </p>
            @if(auth()->user()->isClient())
                <a href="{{ route('dishes.menu-du-jour') }}" style="background-color: #ff6b35; color: white; padding: 12px 32px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; display: inline-block; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                    🍽️ Voir le menu du jour
                </a>
            @endif
        </div>
    @endif
</div>

@if(auth()->user()->isClient())
<script>
/**
 * Real-time polling for client orders
 */
(function() {
    const pollingEndpoint = '{{ route("api.orders.client.polling") }}';
    
    // Update orders list
    function updateClientOrders(data) {
        // Show notification if new order status changed
        if (data.orders.length > 0) {
            // Check for ready orders
            const readyOrders = data.by_status.ready || 0;
            if (readyOrders > 0 && document.body.dataset.hasReadyOrders !== 'true') {
                document.body.dataset.hasReadyOrders = 'true';
                if (window.Toaster) {
                    window.Toaster.show('🎉 Votre commande est prête!', 'success');
                }
            }
        }
    }
    
    // Start polling for clients
    if (window.OrderPoller) {
        window.OrderPoller.pollInterval = 5000; // 5 seconds for clients
        window.OrderPoller.start(pollingEndpoint, updateClientOrders);
    }
    
    // Stop polling when leaving page
    window.addEventListener('beforeunload', () => {
        if (window.OrderPoller) {
            window.OrderPoller.stop();
        }
    });
})();
</script>
@endif

</x-client-sidebar-layout>

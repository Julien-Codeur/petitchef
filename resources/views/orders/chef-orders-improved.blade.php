<x-cook-sidebar-layout>
<div style="padding: 0;">
    <!-- Entête -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px;">Commandes en Attente 📋</h1>
        <p style="color: #666; margin: 0;">Gérez les commandes de votre service aujourd'hui</p>
    </div>

    <!-- Filtres & Stats -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 30px;">
        <div style="background-color: #fff3e0; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">Reçues</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #ff6b35;">{{ $orders->where('status', 'received')->count() }}</p>
        </div>
        <div style="background-color: #fff3e0; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">En préparation</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #ff9800;">{{ $orders->where('status', 'preparing')->count() }}</p>
        </div>
        <div style="background-color: #e8f5e9; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">Prêtes</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #4caf50;">{{ $orders->where('status', 'ready')->count() }}</p>
        </div>
        <div style="background-color: #e3f2fd; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666;">Total montant</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #00677e;">{{ number_format($orders->where('status', '!=', 'cancelled')->sum('total_price'), 2) }}€</p>
        </div>
    </div>

    <!-- Messages -->
    @if ($message = Session::get('success'))
        <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #2e7d32;">
            <p style="margin: 0;">✓ {{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div style="background-color: #ffebee; border-left: 4px solid #d32f2f; border-radius: 4px; padding: 16px; margin-bottom: 20px; color: #d32f2f;">
            <p style="margin: 0;">✗ {{ $message }}</p>
        </div>
    @endif

    <!-- TAB INTERFACE -->
    <div style="margin-bottom: 30px;">
        <!-- Tab buttons -->
        <div style="display: flex; gap: 8px; border-bottom: 2px solid #e0e0e0; margin-bottom: 20px; overflow-x: auto;">
            <button class="tab-btn" data-tab="all" style="padding: 12px 20px; border: none; background: none; font-size: 14px; font-weight: 600; cursor: pointer; color: #00677e; border-bottom: 3px solid #00677e; position: relative; bottom: -2px;">
                📊 Toutes ({{ $orders->count() }})
            </button>
            <button class="tab-btn" data-tab="received" style="padding: 12px 20px; border: none; background: none; font-size: 14px; font-weight: 600; cursor: pointer; color: #999;">
                📥 Reçues ({{ $orders->where('status', 'received')->count() }})
            </button>
            <button class="tab-btn" data-tab="preparing" style="padding: 12px 20px; border: none; background: none; font-size: 14px; font-weight: 600; cursor: pointer; color: #999;">
                ⚙️ Préparation ({{ $orders->where('status', 'preparing')->count() }})
            </button>
            <button class="tab-btn" data-tab="ready" style="padding: 12px 20px; border: none; background: none; font-size: 14px; font-weight: 600; cursor: pointer; color: #999;">
                ✓ Prêtes ({{ $orders->where('status', 'ready')->count() }})
            </button>
            <button class="tab-btn" data-tab="delivered" style="padding: 12px 20px; border: none; background: none; font-size: 14px; font-weight: 600; cursor: pointer; color: #999;">
                ✅ Livrées ({{ $orders->where('status', 'delivered')->count() }})
            </button>
        </div>

        <!-- Search bar -->
        <div style="margin-bottom: 20px;">
            <input type="text" id="search-orders" placeholder="🔍 Chercher par client, #ID ou heure..." style="width: 100%; padding: 10px 16px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 14px; font-family: Arial, sans-serif;">
        </div>

        <!-- Commandes -->
        @if($orders->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($orders->sortBy(function($order) { return ['received' => 0, 'preparing' => 1, 'ready' => 2, 'delivered' => 3, 'cancelled' => 4][$order->status]; }) as $order)
                    <div class="order-card" data-status="{{ $order->status }}" data-search="{{ strtolower($order->client->name . ' #' . $order->id . ' ' . $order->pickup_time) }}" style="background-color: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.3s ease;">
                        <!-- Header -->
                        <div style="display: flex; align-items: center; padding: 16px; gap: 12px; border-bottom: 1px solid #e0e0e0;">
                            <!-- Status Badge -->
                            <div style="padding: 8px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; color: white;
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
                            </div>

                            <!-- Info Commande -->
                            <div style="flex: 1;">
                                <p style="margin: 0; font-size: 14px; font-weight: 700; color: #333;">Commande #{{ $order->id }}</p>
                                <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">
                                    👤 {{ $order->client->name }} • 🕐 {{ $order->pickup_time }} • 📦 {{ $order->items->count() }} article(s)
                                </p>
                            </div>

                            <!-- Prix -->
                            <div style="text-align: right; padding-right: 12px;">
                                <p style="margin: 0; font-size: 14px; color: #666;">Total</p>
                                <p style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #ff6b35;">{{ number_format($order->total_price, 2) }}€</p>
                            </div>

                            <!-- Actions -->
                            <a href="{{ route('orders.show', $order) }}" style="background-color: #00677e; color: white; padding: 10px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#004d63'" onmouseout="this.style.backgroundColor='#00677e'">
                                Détails
                            </a>
                        </div>

                        <!-- Items -->
                        <div style="padding: 16px; background-color: #fafafa;">
                            <p style="margin: 0 0 12px 0; font-size: 12px; font-weight: 600; color: #666; text-transform: uppercase;">Articles ({{ $order->items->count() }})</p>
                            @foreach($order->items as $item)
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #e0e0e0;">
                                    <span style="color: #333; font-size: 13px;">
                                        <strong>{{ $item->quantity }}x</strong> {{ $item->dish->name }}
                                    </span>
                                    <span style="color: #999; font-size: 13px;">{{ number_format($item->unit_price * $item->quantity, 2) }}€</span>
                                </div>
                            @endforeach

                            @if($order->note_client)
                                <div style="margin-top: 12px; padding: 12px; background-color: white; border-left: 3px solid #ff6b35; border-radius: 4px;">
                                    <p style="margin: 0; font-size: 12px; color: #666;"><strong>Note client:</strong> {{ $order->note_client }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Actions rapides -->
                        @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                            <div style="padding: 12px 16px; background-color: #f5f5f5; border-top: 1px solid #e0e0e0; display: flex; gap: 8px;">
                                @if($order->status === 'received')
                                    <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                        @csrf
                                        <input type="hidden" name="status" value="preparing">
                                        <button type="submit" style="width: 100%; background-color: #ff9800; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#f57c00'" onmouseout="this.style.backgroundColor='#ff9800'">
                                            👨‍🍳 Commencer
                                        </button>
                                    </form>
                                @elseif($order->status === 'preparing')
                                    <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                        @csrf
                                        <input type="hidden" name="status" value="ready">
                                        <button type="submit" style="width: 100%; background-color: #4caf50; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#388e3c'" onmouseout="this.style.backgroundColor='#4caf50'">
                                            ✓ Marquér prête
                                        </button>
                                    </form>
                                @elseif($order->status === 'ready')
                                    <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" style="width: 100%; background-color: #9c27b0; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#7b1fa2'" onmouseout="this.style.backgroundColor='#9c27b0'">
                                            📦 Livrée
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($order->status, ['received', 'preparing']))
                                    <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" style="width: 100%; background-color: #d32f2f; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;" onmouseover="this.style.backgroundColor='#b71c1c'" onmouseout="this.style.backgroundColor='#d32f2f'" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande?')">
                                            ✕ Annuler
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 40px; text-align: center;">
                <p style="font-size: 32px; margin: 0 0 16px 0;">📭</p>
                <p style="font-size: 16px; color: #666; margin: 0;">Aucune commande en cours</p>
                <p style="font-size: 13px; color: #999; margin: 8px 0 0 0;">Vos plats attendent les premiers clients !</p>
            </div>
        @endif
    </div>
</div>

<!-- SCRIPT: Tab filtering & search -->
<script>
document.querySelectorAll('.tab-btn').forEach(button => {
    button.addEventListener('click', function() {
        const tab = this.dataset.tab;
        
        // Update button styles
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.style.color = '#999';
            btn.style.borderBottom = 'none';
            btn.style.position = 'relative';
            btn.style.bottom = '0';
        });
        
        this.style.color = '#00677e';
        this.style.borderBottom = '3px solid #00677e';
        this.style.position = 'relative';
        this.style.bottom = '-2px';
        
        // Filter cards
        document.querySelectorAll('.order-card').forEach(card => {
            const status = card.dataset.status;
            if (tab === 'all' || status === tab) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Clear search
        document.getElementById('search-orders').value = '';
    });
});

// Search functionality
document.getElementById('search-orders').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    
    document.querySelectorAll('.order-card').forEach(card => {
        const searchData = card.dataset.search;
        if (searchData.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
</x-cook-sidebar-layout>

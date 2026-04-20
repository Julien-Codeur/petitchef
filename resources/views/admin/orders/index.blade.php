<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">📦 Gestion des Commandes</h1>
        <p style="color: #999; font-size: 14px;">Suivi et gestion de toutes les commandes</p>
    </div>

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 12px; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0;">
        <a href="?filter=all" style="padding: 12px 20px; border-bottom: 3px solid {{ $filter === 'all' ? '#ff6b35' : 'transparent' }}; color: {{ $filter === 'all' ? '#ff6b35' : '#666' }}; text-decoration: none; font-weight: 600; transition: all 0.3s;">Toutes</a>
        <a href="?filter=pending" style="padding: 12px 20px; border-bottom: 3px solid {{ $filter === 'pending' ? '#ff6b35' : 'transparent' }}; color: {{ $filter === 'pending' ? '#ff6b35' : '#666' }}; text-decoration: none; font-weight: 600; transition: all 0.3s;">En attente</a>
        <a href="?filter=completed" style="padding: 12px 20px; border-bottom: 3px solid {{ $filter === 'completed' ? '#ff6b35' : 'transparent' }}; color: {{ $filter === 'completed' ? '#ff6b35' : '#666' }}; text-decoration: none; font-weight: 600; transition: all 0.3s;">Complétées</a>
    </div>

    <!-- Orders Table -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background-color: #f9f9f9; border-bottom: 2px solid #e0e0e0;">
                <tr>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">ID</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Client</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Cuisinier</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Statut</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Montant</th>
                    <th style="padding: 16px; text-align: left; font-weight: 600; color: #666; font-size: 13px;">Date</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600; color: #666; font-size: 13px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr style="border-bottom: 1px solid #e0e0e0; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#f9f9f9'" onmouseout="this.style.backgroundColor='white'">
                        <td style="padding: 16px; font-weight: 600; color: #333;">#{{ $order->id }}</td>
                        <td style="padding: 16px; color: #666;">{{ $order->client->name ?? 'N/A' }}</td>
                        <td style="padding: 16px; color: #666;">{{ $order->cook->name ?? 'N/A' }}</td>
                        <td style="padding: 16px;">
                            @if($order->status === 'pending')
                                <span style="background-color: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">⏳ En attente</span>
                            @elseif($order->status === 'confirmed')
                                <span style="background-color: #d1ecf1; color: #0c5460; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✅ Confirmée</span>
                            @elseif($order->status === 'completed')
                                <span style="background-color: #d4edda; color: #155724; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✓ Complétée</span>
                            @else
                                <span style="background-color: #f8d7da; color: #721c24; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">✗ Annulée</span>
                            @endif
                        </td>
                        <td style="padding: 16px; color: #333; font-weight: 600;">{{ number_format($order->total_price, 2) }}€</td>
                        <td style="padding: 16px; color: #666; font-size: 13px;">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 16px; text-align: center;">
                            <a href="{{ route('admin.orders.show', $order) }}" style="display: inline-block; background-color: #ff6b35; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #999;">
                            <div style="font-size: 48px; margin-bottom: 12px;">📭</div>
                            <p style="font-size: 14px; margin: 0;">Aucune commande trouvée</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $orders->links() }}
    </div>
</div>

<script>
/**
 * Real-time polling for admin orders
 */
(function() {
    const pollingEndpoint = '{{ route("api.orders.admin") }}';
    
    function updateAdminOrders(data) {
        // Could refresh the table or show notifications
        console.log('Admin orders updated:', data);
    }
    
    // Start polling for admins
    if (window.OrderPoller) {
        window.OrderPoller.pollInterval = 6000; // 6 seconds for admin
        window.OrderPoller.start(pollingEndpoint, updateAdminOrders);
    }
    
    // Stop polling when leaving page
    window.addEventListener('beforeunload', () => {
        if (window.OrderPoller) {
            window.OrderPoller.stop();
        }
    });
})();
</script>

</x-admin-sidebar-layout>

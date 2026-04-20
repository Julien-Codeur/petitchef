<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <a href="{{ route('admin.orders.index') }}" style="color: #ff6b35; text-decoration: none; font-weight: 600;">← Retour</a>
        </div>
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">📦 Commande #{{ $order->id }}</h1>
        <p style="color: #999; font-size: 14px;">Détails et gestion de la commande</p>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Order Details -->
        <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px;">
            <!-- Client Info -->
            <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e0e0e0;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 12px; font-family: 'Plus Jakarta Sans', sans-serif;">👤 Client</h3>
                <p style="margin: 0; color: #666; font-weight: 600;">{{ $order->client->name }}</p>
                <p style="margin: 0; color: #999; font-size: 14px;">{{ $order->client->email }}</p>
                <p style="margin: 0; color: #999; font-size: 14px;">{{ $order->client->phone }}</p>
            </div>

            <!-- Cook Info -->
            <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e0e0e0;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 12px; font-family: 'Plus Jakarta Sans', sans-serif;">👨‍🍳 Cuisinier</h3>
                <p style="margin: 0; color: #666; font-weight: 600;">{{ $order->cook->name }}</p>
                <p style="margin: 0; color: #999; font-size: 14px;">{{ $order->cook->email }}</p>
            </div>

            <!-- Items -->
            <div>
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 12px; font-family: 'Plus Jakarta Sans', sans-serif;">🍽️ Plats commandés</h3>
                <div style="background-color: #f9f9f9; border-radius: 8px; padding: 12px;">
                    @forelse($order->items as $item)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e0e0e0;">
                            <div>
                                <p style="margin: 0; font-weight: 600; color: #333;">{{ $item->dish->name }}</p>
                                <p style="margin: 0; font-size: 12px; color: #999;">Quantité: {{ $item->quantity }}</p>
                            </div>
                            <p style="margin: 0; font-weight: 600; color: #ff6b35;">{{ number_format($item->price * $item->quantity, 2) }}€</p>
                        </div>
                    @empty
                        <p style="margin: 0; color: #999; text-align: center; padding: 12px;">Aucun plat</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div>
            <!-- Status Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 12px; font-family: 'Plus Jakarta Sans', sans-serif;">📋 Statut</h3>
                @if($order->status === 'pending')
                    <span style="display: inline-block; background-color: #fff3cd; color: #856404; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">⏳ En attente</span>
                @elseif($order->status === 'confirmed')
                    <span style="display: inline-block; background-color: #d1ecf1; color: #0c5460; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">✅ Confirmée</span>
                @elseif($order->status === 'completed')
                    <span style="display: inline-block; background-color: #d4edda; color: #155724; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">✓ Complétée</span>
                @else
                    <span style="display: inline-block; background-color: #f8d7da; color: #721c24; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600;">✗ Annulée</span>
                @endif
            </div>

            <!-- Total Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">💰 Montant</h3>
                <div style="font-size: 32px; font-weight: 700; color: #ff6b35;">{{ number_format($order->total_price, 2) }}€</div>
                <p style="margin: 0; color: #999; font-size: 12px; margin-top: 8px;">Total TTC</p>
            </div>

            <!-- Date Card -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 12px; font-family: 'Plus Jakarta Sans', sans-serif;">📅 Dates</h3>
                <p style="margin: 0; color: #666; font-size: 13px;">
                    <strong>Créée:</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Mise à jour:</strong> {{ $order->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>
</div>
</x-admin-sidebar-layout>

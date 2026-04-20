<div style="padding: 0;">
    <!-- Entête -->
    <div style="margin-bottom: 20px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">Bienvenue {{ auth()->user()->name }} 👋</h1>
        <p style="color: #999; font-size: 14px;">Découvrez nos cuisiniers et leurs délicieux plats du jour</p>
    </div>

    <!-- Barre Actions -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 16px; margin-bottom: 20px; display: flex; gap: 12px;">
        <a href="{{ route('dishes.menu-du-jour') }}" style="flex: 1; background-color: #ff6b35; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
            🍽️ Menu du Jour
        </a>
        <a href="{{ route('cart.index') }}" style="flex: 1; background-color: #00677e; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; cursor: pointer;" onmouseover="this.style.backgroundColor='#004d63'" onmouseout="this.style.backgroundColor='#00677e'">
            🛒 Mon Panier
            @php
                $cart = session()->get('cart', []);
            @endphp
            @if(count($cart) > 0)
                <span style="display: inline-block; background-color: white; color: #00677e; padding: 2px 8px; border-radius: 10px; font-weight: 700; font-size: 12px; margin-left: 4px;">{{ count($cart) }}</span>
            @endif
        </a>
        <a href="{{ route('orders.index') }}" style="flex: 1; background-color: #705a49; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; cursor: pointer;" onmouseover="this.style.backgroundColor='#5a4738'" onmouseout="this.style.backgroundColor='#705a49'">
            📋 Mes Commandes
        </a>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 30px;">
        <?php
            $myOrders = auth()->user()->ordersAsClient()->count();
            $mySpent = auth()->user()->ordersAsClient()->where('status', '!=', 'cancelled')->sum('total_price');
            $myOrdersInProgress = auth()->user()->ordersAsClient()->whereIn('status', ['received', 'preparing', 'ready'])->count();
        ?>
        <div style="background-color: #e3f2fd; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">Total Commandes</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #00677e;">{{ $myOrders }}</p>
        </div>
        <div style="background-color: #e8f5e9; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">En Cours</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #4caf50;">{{ $myOrdersInProgress }}</p>
        </div>
        <div style="background-color: #fff3e0; border-radius: 8px; padding: 16px; text-align: center;">
            <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">Total Dépensé</p>
            <p style="margin: 8px 0 0 0; font-size: 24px; font-weight: 700; color: #ff6b35;">{{ number_format($mySpent, 2) }}€</p>
        </div>
    </div>

    <!-- Mes Commandes Récentes -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 30px;">
        <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">Mes Commandes Récentes</h2>
        
        <?php
            $recentOrders = auth()->user()->ordersAsClient()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        ?>

        @if($recentOrders->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($recentOrders as $order)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border: 1px solid #e0e0e0; border-radius: 8px; background-color: #f9f9f9;">
                        <div>
                            <p style="margin: 0; font-weight: 700; color: #333;">Commande #{{ $order->id }}</p>
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">
                                {{ $order->created_at->format('d/m/Y H:i') }} • Chef: {{ $order->cook->name }}
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="text-align: right;">
                                <span style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: white;
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
                                <p style="margin: 6px 0 0 0; font-size: 14px; font-weight: 700; color: #ff6b35;">{{ number_format($order->total_price, 2) }}€</p>
                            </div>
                            <a href="{{ route('orders.show', $order) }}" style="background-color: #f5f5f5; color: #333; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; border: 1px solid #e0e0e0;">
                                Détails →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="text-align: center; margin-top: 16px;">
                <a href="{{ route('orders.index') }}" style="color: #ff6b35; font-size: 13px; font-weight: 600; text-decoration: none;">Voir toutes mes commandes →</a>
            </div>
        @else
            <div style="text-align: center; padding: 20px; color: #999;">
                <p style="margin: 0; font-size: 14px;">📭 Aucune commande pour le moment</p>
                <p style="margin: 8px 0 0 0; font-size: 12px;">Commencez à explorer nos plats !</p>
            </div>
        @endif
    </div>

    <!-- Cuisiniers Populaires -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
        <h2 style="margin: 0 0 20px 0; font-size: 16px; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">Cuisiniers du Jour ⭐</h2>
        
        <?php
            $cooks = \App\Models\User::whereHas('dishes', function ($query) {
                $query->today()->active();
            })->limit(6)->get();
        ?>

        @if($cooks->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px;">
                @foreach($cooks as $cook)
                    <?php
                        $cookDishCount = $cook->dishes()->today()->active()->count();
                        $cookOrders = $cook->ordersAsCook()->count();
                    ?>
                    <div style="background-color: #f9f9f9; border-radius: 8px; padding: 16px; text-align: center; border: 1px solid #e0e0e0;">
                        <div style="width: 64px; height: 64px; background-color: #ff6b35; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 28px; margin: 0 auto 12px;">
                            {{ substr($cook->name, 0, 1) }}
                        </div>
                        <h4 style="margin: 0 0 4px 0; font-weight: 700; color: #333; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $cook->name }}</h4>
                        <p style="margin: 0 0 8px 0; font-size: 12px; color: #999;">{{ $cookDishCount }} plat{{ $cookDishCount > 1 ? 's' : '' }} disponible{{ $cookDishCount > 1 ? 's' : '' }}</p>
                        <p style="margin: 0 0 12px 0; font-size: 11px; color: #666;">{{ $cookOrders }} commande{{ $cookOrders > 1 ? 's' : '' }}</p>
                        <a href="{{ route('dishes.menu-du-jour') }}" style="display: inline-block; background-color: #ff6b35; color: white; padding: 8px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-decoration: none;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
                            Voir ses plats
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 20px; color: #999;">
                <p style="margin: 0; font-size: 14px;">🔍 Aucun cuisinier disponible pour le moment</p>
            </div>
        @endif
    </div>
</div>

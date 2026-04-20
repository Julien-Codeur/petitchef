<div style="padding: 0;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">Tableau de Bord Cuisinier 👨‍🍳</h1>
        <p style="color: #999; font-size: 14px;">Gérez vos commandes et optimisez votre production.</p>
    </div>

    <!-- Quick Stats -->
    <?php
        $totalOrders = auth()->user()->ordersAsCook()->whereDate('created_at', today())->count();
        $receivedOrders = auth()->user()->ordersAsCook()->where('status', 'received')->count();
        $preparingOrders = auth()->user()->ordersAsCook()->where('status', 'preparing')->count();
        $readyOrders = auth()->user()->ordersAsCook()->where('status', 'ready')->count();
        $todayRevenue = auth()->user()->ordersAsCook()->whereDate('created_at', today())->where('status', '!=', 'cancelled')->sum('total_price');
        $todayDishes = auth()->user()->dishes()->where('served_date', today())->where('is_active', true)->count();
        $totalStock = auth()->user()->dishes()->where('served_date', today())->sum('available_qty');
    ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #ff6b35; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $receivedOrders }}</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">Commandes Reçues</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">À traiter</div>
        </div>

        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #ff9800; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $preparingOrders }}</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">En Préparation</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">En cours</div>
        </div>

        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #4caf50; font-family: 'Plus Jakarta Sans', sans-serif;">{{ $readyOrders }}</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">Prêtes à Livrer</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">En attente</div>
        </div>

        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #9c27b0; font-family: 'Plus Jakarta Sans', sans-serif;">{{ number_format($todayRevenue, 0) }}€</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">Revenus Aujourd'hui</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">{{ $totalOrders }} commandes</div>
        </div>
    </div>

    <!-- Plats & Stock -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 30px;">
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <p style="margin: 0; font-size: 12px; color: #999; font-weight: 600;">Plats en ligne aujourd'hui</p>
            <p style="margin: 8px 0 0 0; font-size: 28px; font-weight: 700; color: #00677e;">{{ $todayDishes }}</p>
        </div>
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <p style="margin: 0; font-size: 12px; color: #999; font-weight: 600;">Stock total disponible</p>
            <p style="margin: 8px 0 0 0; font-size: 28px; font-weight: 700; color: #705a49;">{{ $totalStock }} portions</p>
        </div>
    </div>

    <!-- Actions rapides -->
    <div style="display: flex; gap: 12px; margin-bottom: 30px;">
        <a href="{{ route('dishes.index') }}" style="flex: 1; background-color: #ff6b35; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; cursor: pointer;" onmouseover="this.style.backgroundColor='#ff5a1f'" onmouseout="this.style.backgroundColor='#ff6b35'">
            🍽️ Gérer mes plats
        </a>
        <a href="{{ route('dishes.create') }}" style="flex: 1; background-color: #00677e; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; cursor: pointer;" onmouseover="this.style.backgroundColor='#004d63'" onmouseout="this.style.backgroundColor='#00677e'">
            ➕ Ajouter un plat
        </a>
        <a href="{{ route('orders.chef') }}" style="flex: 1; background-color: #705a49; color: white; padding: 12px 16px; border-radius: 20px; font-size: 14px; font-weight: 700; text-decoration: none; text-align: center; cursor: pointer;" onmouseover="this.style.backgroundColor='#5a4738'" onmouseout="this.style.backgroundColor='#705a49'">
            📋 Voir mes commandes
        </a>
    </div>

    <!-- Commandes du jour -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">Commandes Récentes Aujourd'hui</h2>
        
        <?php
            $recentOrders = auth()->user()->ordersAsCook()
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        ?>

        @if($recentOrders->count() > 0)
            @foreach($recentOrders as $order)
                <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; margin-bottom: 12px; @if($order->status === 'received') background-color: #fff3ed; @elseif($order->status === 'preparing') background-color: #fffbf0; @elseif($order->status === 'ready') background-color: #f0fdf4; @endif">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <div>
                            <h4 style="font-weight: 700; color: #333; margin-bottom: 4px; font-family: 'Plus Jakarta Sans', sans-serif;">Commande #{{ $order->id }}</h4>
                            <p style="color: #999; font-size: 12px;">👤 {{ $order->client->name }} • 🕐 {{ $order->pickup_time->format('H:i') }}</p>
                        </div>
                        <div style="text-align: right;">
                            <span style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; color: white;
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
                                @endswitch">
                                {{ $order->getStatusLabel() }}
                            </span>
                            <p style="margin: 8px 0 0 0; font-size: 14px; font-weight: 700; color: #ff6b35;">{{ number_format($order->total_price, 2) }}€</p>
                        </div>
                    </div>

                    <div style="padding: 8px 0; margin-bottom: 12px; border-top: 1px solid #e0e0e0; border-bottom: 1px solid #e0e0e0;">
                        <p style="margin: 0; font-size: 12px; color: #666; font-weight: 600;">Items ({{ $order->items->count() }})</p>
                        @foreach($order->items as $item)
                            <p style="margin: 4px 0 0 0; font-size: 12px; color: #999;">{{ $item->quantity }}x {{ $item->dish->name }}</p>
                        @endforeach
                    </div>

                    @if($order->status !== 'cancelled' && $order->status !== 'delivered')
                        <div style="display: flex; gap: 8px;">
                            @if($order->status === 'received')
                                <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                    @csrf
                                    <input type="hidden" name="status" value="preparing">
                                    <button type="submit" style="width: 100%; background-color: #ff9800; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;">
                                        👨‍🍳 Commencer
                                    </button>
                                </form>
                            @elseif($order->status === 'preparing')
                                <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                    @csrf
                                    <input type="hidden" name="status" value="ready">
                                    <button type="submit" style="width: 100%; background-color: #4caf50; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;">
                                        ✓ Marquér prête
                                    </button>
                                </form>
                            @elseif($order->status === 'ready')
                                <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                    @csrf
                                    <input type="hidden" name="status" value="delivered">
                                    <button type="submit" style="width: 100%; background-color: #9c27b0; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;">
                                        📦 Livrée
                                    </button>
                                </form>
                            @endif

                            @if(in_array($order->status, ['received', 'preparing']))
                                <form action="{{ route('orders.update-status', $order) }}" method="PATCH" style="flex: 1;">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" style="width: 100%; background-color: #d32f2f; color: white; padding: 8px 12px; border-radius: 4px; font-size: 12px; font-weight: 600; border: none; cursor: pointer;" onclick="return confirm('Êtes-vous sûr?')">
                                        ✕ Annuler
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
            
            <div style="text-align: center; margin-top: 16px;">
                <a href="{{ route('orders.chef') }}" style="background-color: #f5f5f5; color: #333; padding: 10px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; border: 1px solid #e0e0e0;">
                    Voir toutes mes commandes →
                </a>
            </div>
        @else
            <div style="padding: 30px; text-align: center; color: #999;">
                <p style="margin: 0; font-size: 14px;">📭 Aucune commande pour le moment</p>
                <p style="margin: 8px 0 0 0; font-size: 12px;">Vos plats attendent les premiers clients</p>
            </div>
        @endif
    </div>

    <!-- Charts Section -->
    <div style="margin-top: 30px;">
        <h2 style="font-size: 20px; font-weight: 700; color: #333; margin-bottom: 20px; font-family: 'Plus Jakarta Sans', sans-serif;">📊 Statistiques en Temps Réel</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <!-- Revenue Trend Chart -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px;">Tendance des Revenus (7 derniers jours)</h3>
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>

            <!-- Orders by Status Chart -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px;">Commandes par Statut (Aujourd'hui)</h3>
                <canvas id="statusChart" style="max-height: 300px;"></canvas>
            </div>

            <!-- Top Dishes Chart -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px;">Top 5 Mes Plats les Plus Vendus</h3>
                <canvas id="dishesChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Fetch and display chef stats charts
    async function loadChefCharts() {
        try {
            const response = await fetch('/api/stats/chef', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });
            const data = await response.json();

            // Revenue Trend Chart
            if (data.revenueTrend && data.revenueTrend.length > 0) {
                const revenueCtx = document.getElementById('revenueChart').getContext('2d');
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: data.revenueTrend.map(item => {
                            const date = new Date(item.date);
                            return date.toLocaleDateString('fr-FR', { month: 'short', day: 'numeric' });
                        }),
                        datasets: [{
                            label: 'Revenus (€)',
                            data: data.revenueTrend.map(item => parseFloat(item.revenue) || 0),
                            borderColor: '#ff6b35',
                            backgroundColor: 'rgba(255, 107, 53, 0.1)',
                            tension: 0.3,
                            fill: true,
                            pointBackgroundColor: '#ff6b35',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                labels: { font: { size: 12 } }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 11 } }
                            },
                            x: {
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            }

            // Orders by Status Chart
            if (data.ordersByStatus) {
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                const statusLabels = {
                    'pending': 'En attente',
                    'in_preparation': 'En préparation',
                    'ready': 'Prête',
                    'delivered': 'Livrée',
                    'cancelled': 'Annulée'
                };
                const colors = {
                    'pending': '#ffc107',
                    'in_preparation': '#ff9800',
                    'ready': '#2196f3',
                    'delivered': '#4caf50',
                    'cancelled': '#f44336'
                };
                
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: Object.entries(data.ordersByStatus).map(([key]) => statusLabels[key] || key),
                        datasets: [{
                            data: Object.values(data.ordersByStatus),
                            backgroundColor: Object.keys(data.ordersByStatus).map(key => colors[key] || '#999'),
                            borderColor: '#fff',
                            borderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { font: { size: 11 }, padding: 15 }
                            }
                        }
                    }
                });
            }

            // Top Dishes Chart
            if (data.topDishes && data.topDishes.length > 0) {
                const dishCtx = document.getElementById('dishesChart').getContext('2d');
                new Chart(dishCtx, {
                    type: 'bar',
                    data: {
                        labels: data.topDishes.map(dish => dish.name),
                        datasets: [{
                            label: 'Quantité vendue',
                            data: data.topDishes.map(dish => parseInt(dish.quantity_sold) || 0),
                            backgroundColor: '#ff6b35',
                            borderColor: '#e55a24',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { font: { size: 11 } }
                            },
                            y: {
                                ticks: { font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        } catch (error) {
            console.error('Error loading charts:', error);
        }
    }

    // Load charts when page is ready
    document.addEventListener('DOMContentLoaded', loadChefCharts);
</script>

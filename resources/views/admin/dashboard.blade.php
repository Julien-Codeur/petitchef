<x-admin-sidebar-layout>
<div style="padding: 0;">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">🔐 Tableau de Bord Admin</h1>
        <p style="color: #999; font-size: 14px;">Vue globale et gestion de la plateforme PetitChef</p>
    </div>

    <!-- Statistics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <!-- Cooks -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">👨‍🍳 Cuisiniers</div>
            <div style="font-size: 32px; font-weight: 700; color: #ff6b35; margin-bottom: 8px;">{{ $stats['total_cooks'] }}</div>
            <div style="font-size: 12px; color: #666;">
                ✅ {{ $stats['verified_cooks'] }} approuvés<br>
                ⏳ {{ $stats['pending_cooks'] }} en attente
            </div>
        </div>

        <!-- Clients -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">👥 Clients</div>
            <div style="font-size: 32px; font-weight: 700; color: #00677e; margin-bottom: 8px;">{{ $stats['total_clients'] }}</div>
            <div style="font-size: 12px; color: #666;">Utilisateurs actifs</div>
        </div>

        <!-- Orders -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">📦 Commandes</div>
            <div style="font-size: 32px; font-weight: 700; color: #705a49; margin-bottom: 8px;">{{ $stats['total_orders'] }}</div>
            <div style="font-size: 12px; color: #666;">Total toutes périodes</div>
        </div>

        <!-- Dishes -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 14px; color: #999; font-weight: 600; margin-bottom: 12px;">🍽️ Plats</div>
            <div style="font-size: 32px; font-weight: 700; color: #d4a574; margin-bottom: 8px;">{{ $stats['total_dishes'] }}</div>
            <div style="font-size: 12px; color: #666;">Créés par les cuisiniers</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 30px;">
        <a href="{{ route('admin.cooks.index', ['filter' => 'pending']) }}" style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0; text-decoration: none; text-align: center; transition: all 0.3s;">
            <div style="font-size: 24px; margin-bottom: 8px;">⏳</div>
            <div style="font-weight: 600; color: #333;">Cooks en attente</div>
            <div style="font-size: 28px; font-weight: 700; color: #ff9800; margin-top: 8px;">{{ $stats['pending_cooks'] }}</div>
        </a>

        <a href="{{ route('admin.cooks.index', ['filter' => 'verified']) }}" style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0; text-decoration: none; text-align: center; transition: all 0.3s;">
            <div style="font-size: 24px; margin-bottom: 8px;">✅</div>
            <div style="font-weight: 600; color: #333;">Cooks approuvés</div>
            <div style="font-size: 28px; font-weight: 700; color: #4caf50; margin-top: 8px;">{{ $stats['verified_cooks'] }}</div>
        </a>

        <a href="{{ route('admin.orders.index') }}" style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0; text-decoration: none; text-align: center; transition: all 0.3s;">
            <div style="font-size: 24px; margin-bottom: 8px;">📦</div>
            <div style="font-weight: 600; color: #333;">Toutes les commandes</div>
            <div style="font-size: 28px; font-weight: 700; color: #705a49; margin-top: 8px;">{{ $stats['total_orders'] }}</div>
        </a>

        <a href="{{ route('admin.users.index') }}" style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0; text-decoration: none; text-align: center; transition: all 0.3s;">
            <div style="font-size: 24px; margin-bottom: 8px;">👥</div>
            <div style="font-weight: 600; color: #333;">Gestion utilisateurs</div>
            <div style="font-size: 28px; font-weight: 700; color: #1976d2; margin-top: 8px;">{{ $stats['total_clients'] + $stats['total_cooks'] }}</div>
        </a>
    </div>

    <!-- Admin Actions -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 30px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #333; margin: 0 0 16px 0; font-family: 'Plus Jakarta Sans', sans-serif;">Actions Admin</h2>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="{{ route('admin.cooks.index', ['filter' => 'pending']) }}" style="display: block; background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 12px 16px; border-radius: 4px; text-decoration: none; color: #333;">
                <strong>⏳ {{ $stats['pending_cooks'] }} cuisinier(s) en attente de validation</strong>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #666;">Cliquez pour examiner et approuver/rejeter les profils</p>
            </a>
        </div>
    </div>

    <!-- Info Section -->
    <div style="background-color: #e3f2fd; border-left: 4px solid #1976d2; border-radius: 4px; padding: 16px;">
        <p style="margin: 0; color: #0d47a1; font-weight: 600; margin-bottom: 8px;">ℹ️ Gestion de la plateforme</p>
        <ul style="margin: 0; padding-left: 20px; color: #333; font-size: 13px;">
            <li>Approuvez les cuisiniers pour leur permettre de créer des plats</li>
            <li>Visualisez toutes les commandes et plats de la plateforme</li>
            <li>Gérez les utilisateurs et leurs permissions</li>
            <li>Supervisez les statistiques en temps réel</li>
        </ul>
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
                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px;">Commandes par Statut</h3>
                <canvas id="statusChart" style="max-height: 300px;"></canvas>
            </div>

            <!-- Top Dishes Chart -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px;">Top 5 Plats les Plus Vendus</h3>
                <canvas id="dishesChart" style="max-height: 300px;"></canvas>
            </div>

            <!-- Cook Performance Chart -->
            <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
                <h3 style="font-size: 14px; font-weight: 700; color: #333; margin-bottom: 15px;">Performance des Cuisiniers (Commandes Livrées)</h3>
                <canvas id="cookChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Fetch and display admin stats charts
    async function loadAdminCharts() {
        try {
            const response = await fetch('/api/stats/admin', {
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
                            data: data.topDishes.map(dish => parseInt(dish.total_quantity) || 0),
                            backgroundColor: '#d4a574',
                            borderColor: '#b8956a',
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

            // Cook Performance Chart
            if (data.cookPerformance && data.cookPerformance.length > 0) {
                const cookCtx = document.getElementById('cookChart').getContext('2d');
                new Chart(cookCtx, {
                    type: 'bar',
                    data: {
                        labels: data.cookPerformance.map(cook => cook.name),
                        datasets: [{
                            label: 'Commandes livrées',
                            data: data.cookPerformance.map(cook => parseInt(cook.completed_orders) || 0),
                            backgroundColor: '#00677e',
                            borderColor: '#004d61',
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
    document.addEventListener('DOMContentLoaded', loadAdminCharts);
</script>
</x-admin-sidebar-layout>

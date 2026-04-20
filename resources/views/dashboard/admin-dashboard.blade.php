<div style="padding: 0;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700; color: #333; margin-bottom: 8px; font-family: 'Plus Jakarta Sans', sans-serif;">Bienvenue, {{ auth()->user()->name }}! 👋</h1>
        <p style="color: #999; font-size: 14px;">Retrouvez un aperçu de toutes les activités et données importantes.</p>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <!-- Card: Cuisiniers -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #ff6b35; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px;">12</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">Cuisiniers Actifs</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">+2 cette semaine</div>
        </div>

        <!-- Card: Commandes -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #705a49; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px;">247</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">Commandes ce mois</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">+18% vs mois dernier</div>
        </div>

        <!-- Card: Revenus -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #00677e; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px;">3.240€</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">Revenus ce mois</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">Trend positif</div>
        </div>

        <!-- Card: Clients -->
        <div style="background-color: white; border-radius: 8px; padding: 20px; border: 1px solid #e0e0e0;">
            <div style="font-size: 32px; font-weight: 700; color: #d4a574; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 8px;">856</div>
            <div style="font-size: 13px; color: #666; font-weight: 500;">Clients Inscrits</div>
            <div style="font-size: 12px; color: #999; margin-top: 8px;">+42 nouveaux</div>
        </div>
    </div>

    <!-- Alerts Section -->
    <div style="background-color: #fff3ed; border-left: 4px solid #ff6b35; border-radius: 8px; padding: 16px; margin-bottom: 30px;">
        <h3 style="font-size: 15px; font-weight: 700; color: #ff6b35; margin-bottom: 10px; font-family: 'Plus Jakarta Sans', sans-serif;">⚠️ Alertes Importantes</h3>
        <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="padding: 8px 0; color: #333; font-size: 13px;">📌 3 commandes en retard à traiter</li>
            <li style="padding: 8px 0; color: #333; font-size: 13px;">📌 1 cuisinier signalé comme indisponible</li>
            <li style="padding: 8px 0; color: #333; font-size: 13px;">📌 Maintenance prévue dimanche 21h-23h</li>
        </ul>
    </div>

    <!-- Recent Orders Section -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px; margin-bottom: 30px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">Commandes Récentes</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e0e0e0;">
                    <th style="text-align: left; padding: 12px; color: #666; font-weight: 600; font-size: 12px;">ID COMMANDE</th>
                    <th style="text-align: left; padding: 12px; color: #666; font-weight: 600; font-size: 12px;">CLIENT</th>
                    <th style="text-align: left; padding: 12px; color: #666; font-weight: 600; font-size: 12px;">CUISINIER</th>
                    <th style="text-align: left; padding: 12px; color: #666; font-weight: 600; font-size: 12px;">MONTANT</th>
                    <th style="text-align: left; padding: 12px; color: #666; font-weight: 600; font-size: 12px;">STATUT</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 12px; color: #333; font-weight: 600;">#001245</td>
                    <td style="padding: 12px; color: #333;">Marie Dupont</td>
                    <td style="padding: 12px; color: #333;">Jean Legrand</td>
                    <td style="padding: 12px; color: #ff6b35; font-weight: 600;">48€</td>
                    <td style="padding: 12px;"><span style="background-color: #e8f5e9; color: #2e7d32; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">✓ Livrée</span></td>
                </tr>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 12px; color: #333; font-weight: 600;">#001244</td>
                    <td style="padding: 12px; color: #333;">Pierre Martin</td>
                    <td style="padding: 12px; color: #333;">Sophie Bernard</td>
                    <td style="padding: 12px; color: #ff6b35; font-weight: 600;">56€</td>
                    <td style="padding: 12px;"><span style="background-color: #fff3e0; color: #e65100; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">⏳ En préparation</span></td>
                </tr>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 12px; color: #333; font-weight: 600;">#001243</td>
                    <td style="padding: 12px; color: #333;">Anne Leclerc</td>
                    <td style="padding: 12px; color: #333;">Marc Rousseau</td>
                    <td style="padding: 12px; color: #ff6b35; font-weight: 600;">72€</td>
                    <td style="padding: 12px;"><span style="background-color: #e1f5fe; color: #01579b; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">📦 Prête</span></td>
                </tr>
                <tr style="border-bottom: 1px solid #e0e0e0;">
                    <td style="padding: 12px; color: #333; font-weight: 600;">#001242</td>
                    <td style="padding: 12px; color: #333;">Luc Gautier</td>
                    <td style="padding: 12px; color: #333;">Jean Legrand</td>
                    <td style="padding: 12px; color: #ff6b35; font-weight: 600;">64€</td>
                    <td style="padding: 12px;"><span style="background-color: #e0e0e0; color: #555; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">❌ Annulée</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Top Chefs Section -->
    <div style="background-color: white; border-radius: 8px; border: 1px solid #e0e0e0; padding: 20px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #333; margin-bottom: 16px; font-family: 'Plus Jakarta Sans', sans-serif;">Meilleurs Cuisiniers</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 12px;">👨‍🍳</div>
                <h4 style="font-weight: 600; color: #333; margin-bottom: 4px; font-family: 'Plus Jakarta Sans', sans-serif;">Jean Legrand</h4>
                <p style="color: #999; font-size: 12px; margin-bottom: 12px;">125 commandes</p>
                <div style="display: flex; gap: 4px; justify-content: center; margin-bottom: 8px;">
                    <span style="color: #ffc107;">★★★★★</span>
                </div>
                <div style="font-size: 12px; color: #666; font-weight: 600;">4.8/5 (143 avis)</div>
            </div>

            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 12px;">👨‍🍳</div>
                <h4 style="font-weight: 600; color: #333; margin-bottom: 4px; font-family: 'Plus Jakarta Sans', sans-serif;">Sophie Bernard</h4>
                <p style="color: #999; font-size: 12px; margin-bottom: 12px;">98 commandes</p>
                <div style="display: flex; gap: 4px; justify-content: center; margin-bottom: 8px;">
                    <span style="color: #ffc107;">★★★★☆</span>
                </div>
                <div style="font-size: 12px; color: #666; font-weight: 600;">4.6/5 (87 avis)</div>
            </div>

            <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 16px; text-align: center;">
                <div style="font-size: 40px; margin-bottom: 12px;">👨‍🍳</div>
                <h4 style="font-weight: 600; color: #333; margin-bottom: 4px; font-family: 'Plus Jakarta Sans', sans-serif;">Marc Rousseau</h4>
                <p style="color: #999; font-size: 12px; margin-bottom: 12px;">87 commandes</p>
                <div style="display: flex; gap: 4px; justify-content: center; margin-bottom: 8px;">
                    <span style="color: #ffc107;">★★★★☆</span>
                </div>
                <div style="font-size: 12px; color: #666; font-weight: 600;">4.7/5 (102 avis)</div>
            </div>
        </div>
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

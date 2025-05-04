@extends('layouts.admin')

@section('content')
<h2>
    <i class="fas fa-chart-line me-2"></i>Statistiques et Analyses
</h2>

<div class="row mb-4">
    <!-- Total des ventes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total des ventes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_sales'], 2) }} €</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-euro-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total des utilisateurs -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Utilisateurs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total des commandes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Commandes</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bibliothèque -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Bibliothèque</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $stats['total_songs'] }} titres / {{ $stats['total_albums'] }} albums
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-music fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Aujourd'hui -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Aujourd'hui</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <h6 class="text-muted">Nouveaux utilisateurs</h6>
                        <h2 class="mb-0">{{ $stats['new_users_today'] }}</h2>
                    </div>
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <h6 class="text-muted">Commandes</h6>
                        <h2 class="mb-0">{{ $stats['orders_today'] }}</h2>
                    </div>
                    <div class="col-md-4 text-center">
                        <h6 class="text-muted">Ventes</h6>
                        <h2 class="mb-0">{{ number_format($stats['sales_today'], 2) }} €</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row">
    <!-- Graphique des ventes -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Ventes des 30 derniers jours</h3>
            </div>
            <div class="card-body">
                <canvas id="salesChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Graphique des utilisateurs -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Inscriptions par mois</h3>
            </div>
            <div class="card-body">
                <canvas id="userRegistrationChart" width="100%" height="40"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top chansons et albums -->
<div class="row">
    <!-- Top 5 des chansons -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Top 5 des chansons les plus vendues</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Artiste</th>
                                <th>Ventes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSongs as $song)
                                <tr>
                                    <td>{{ $song->title }}</td>
                                    <td>{{ $song->artist ?? 'Inconnu' }}</td>
                                    <td>{{ $song->sales_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucune donnée disponible</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top 5 des albums -->
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Top 5 des albums les plus vendus</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Artiste</th>
                                <th>Ventes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topAlbums as $album)
                                <tr>
                                    <td>{{ $album->title }}</td>
                                    <td>{{ $album->artist ?? 'Inconnu' }}</td>
                                    <td>{{ $album->sales_count }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucune donnée disponible</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique des ventes
        const salesData = @json($salesData);
        const dates = salesData.map(item => item.date);
        const sales = salesData.map(item => item.total_sales);
        const orders = salesData.map(item => item.order_count);
        
        // Graphique des ventes
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [
                    {
                        label: 'Ventes (€)',
                        data: sales,
                        backgroundColor: 'rgba(78, 115, 223, 0.05)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                        pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Commandes',
                        data: orders,
                        backgroundColor: 'rgba(28, 200, 138, 0.05)',
                        borderColor: 'rgba(28, 200, 138, 1)',
                        pointRadius: 3,
                        pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: 'rgba(28, 200, 138, 1)',
                        pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        ticks: {
                            beginAtZero: true
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });
        
        // Données pour le graphique des utilisateurs
        const usersByMonth = @json($usersByMonth);
        const months = usersByMonth.map(item => {
            const [year, month] = item.month.split('-');
            return new Date(year, month - 1).toLocaleString('fr-FR', { month: 'short', year: 'numeric' });
        });
        const users = usersByMonth.map(item => item.user_count);
        
        // Graphique des inscriptions utilisateurs
        const userCtx = document.getElementById('userRegistrationChart').getContext('2d');
        new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Nouveaux utilisateurs',
                    data: users,
                    backgroundColor: 'rgba(54, 185, 204, 0.8)',
                    borderColor: 'rgba(54, 185, 204, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        ticks: {
                            beginAtZero: true,
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection

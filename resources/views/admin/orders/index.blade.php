@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Commandes 🛒</h2>
        <a href="{{ route('admin.orders.report') }}" class="btn btn-primary">
            <i class="fas fa-file-csv me-1"></i> Télécharger rapport CSV
        </a>
    </div>

    @if(session('admin_success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('admin_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Carte de statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light text-dark mb-3 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Commandes</h5>
                    <p class="display-5 fw-bold my-3">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark mb-3 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">En attente</h5>
                    <p class="display-5 fw-bold my-3">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white mb-3 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Payées</h5>
                    <p class="display-5 fw-bold my-3">{{ $stats['paid'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white mb-3 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Chiffre d'affaires</h5>
                    <p class="display-5 fw-bold my-3">{{ number_format($stats['total_revenue'], 2) }} €</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="row align-items-end">
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="processing" {{ $status == 'processing' ? 'selected' : '' }}>En traitement</option>
                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Payée</option>
                        <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Échouée</option>
                        <option value="refunded" {{ $status == 'refunded' ? 'selected' : '' }}>Remboursée</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <label for="user" class="form-label">Client</label>
                    <select name="user" id="user" class="form-select">
                        <option value="">Tous les clients</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex">
                    <button type="submit" class="btn btn-primary me-2 flex-grow-1">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    @if($status || $userId)
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary flex-grow-1">
                            <i class="fas fa-times me-1"></i> Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">N° Commande</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Nb Articles</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-3">
                                    <span class="fw-bold">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($order->user)
                                            <div>
                                                <div class="fw-bold">{{ $order->user->name }}</div>
                                                <div class="small text-secondary">{{ $order->user->email }}</div>
                                            </div>
                                        @else
                                            <span class="text-muted">Client supprimé</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $order->orderItems->count() }}</td>
                                <td>{{ number_format($order->total_price, 2) }} €</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning text-dark">En attente</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge bg-info text-dark">En traitement</span>
                                    @elseif($order->status == 'paid')
                                        <span class="badge bg-success">Payée</span>
                                    @elseif($order->status == 'failed')
                                        <span class="badge bg-danger">Échouée</span>
                                    @elseif($order->status == 'refunded')
                                        <span class="badge bg-secondary">Remboursée</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Détails
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <h5>Aucune commande trouvée</h5>
                                        <p class="text-muted">Aucune commande ne correspond aux critères de filtrage</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $orders->withQueryString()->links() }}
    </div>
</div>
@endsection

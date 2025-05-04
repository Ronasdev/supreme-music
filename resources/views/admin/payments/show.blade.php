@extends('layouts.admin')

@section('title', 'Détails du paiement #' . $order->id)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Détails du paiement #{{ $order->id }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">Paiements</a></li>
        <li class="breadcrumb-item active">Paiement #{{ $order->id }}</li>
    </ol>

    @if(session('admin_success'))
        <div class="alert alert-success">
            {{ session('admin_success') }}
        </div>
    @endif

    @if(session('admin_error'))
        <div class="alert alert-danger">
            {{ session('admin_error') }}
        </div>
    @endif

    @if(session('admin_info'))
        <div class="alert alert-info">
            {{ session('admin_info') }}
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <!-- Informations de paiement -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-credit-card me-1"></i>
                        Informations de paiement
                    </div>
                    <div class="d-flex">
                        <!-- Bouton de vérification de statut -->
                        <form action="{{ route('admin.payments.check', $order) }}" method="POST" class="me-2">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-sync-alt"></i> Vérifier le statut
                            </button>
                        </form>
                        
                        <!-- Bouton d'accès à la commande -->
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-shopping-cart"></i> Voir la commande
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="fw-bold">Numéro de transaction</div>
                            <div>{{ $order->transaction_id }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Méthode de paiement</div>
                            <div>{{ $order->payment_method ?? 'Non spécifiée' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Statut</div>
                            <div>
                                @if($order->status == 'paid')
                                    <span class="badge bg-success">Payé</span>
                                @elseif($order->status == 'pending')
                                    <span class="badge bg-warning">En attente</span>
                                @elseif($order->status == 'failed')
                                    <span class="badge bg-danger">Échoué</span>
                                @elseif($order->status == 'refunded')
                                    <span class="badge bg-info">Remboursé</span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="fw-bold">Date de création</div>
                            <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Date de paiement</div>
                            <div>{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : 'Non payée' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fw-bold">Date de remboursement</div>
                            <div>{{ $order->refunded_at ? $order->refunded_at->format('d/m/Y H:i') : 'Non remboursée' }}</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="fw-bold">Montant</div>
                            <div>{{ number_format($order->total_price, 2, ',', ' ') }} €</div>
                        </div>
                        <div class="col-md-8">
                            <div class="fw-bold">Raison du remboursement</div>
                            <div>{{ $order->refund_reason ?? 'Non remboursée' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Articles de la commande -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Articles commandés
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Type</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        @if($item->item)
                                            {{ $item->item->title }}
                                        @else
                                            Produit supprimé
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->item_type == App\Models\Song::class)
                                            <span class="badge bg-primary">Chanson</span>
                                        @elseif($item->item_type == App\Models\Album::class)
                                            <span class="badge bg-info">Album</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item->item_type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price, 2, ',', ' ') }} €</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total</th>
                                <th>{{ number_format($order->total_price, 2, ',', ' ') }} €</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Historique des logs de paiement -->
            @if(count($paymentLogs) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-history me-1"></i>
                        Historique des transactions
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Message</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentLogs as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            @if($log->status == 'paid')
                                                <span class="badge bg-success">Payé</span>
                                            @elseif($log->status == 'pending')
                                                <span class="badge bg-warning">En attente</span>
                                            @elseif($log->status == 'failed')
                                                <span class="badge bg-danger">Échoué</span>
                                            @elseif($log->status == 'refunded')
                                                <span class="badge bg-info">Remboursé</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $log->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $log->message }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <!-- Informations du client -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations du client
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="fw-bold">Nom</div>
                        <div>{{ $order->user->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-bold">Email</div>
                        <div>{{ $order->user->email }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="fw-bold">Date d'inscription</div>
                        <div>{{ $order->user->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-primary">
                            Voir le profil
                        </a>
                    </div>
                </div>
            </div>

            <!-- Actions sur le paiement -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cogs me-1"></i>
                    Actions
                </div>
                <div class="card-body">
                    @if($order->status == 'paid')
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Vous pouvez rembourser ce paiement si nécessaire.
                        </div>
                        <form action="{{ route('admin.payments.refund', $order) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir rembourser ce paiement?');">
                            @csrf
                            <div class="mb-3">
                                <label for="reason" class="form-label">Raison du remboursement</label>
                                <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-undo"></i> Rembourser le paiement
                            </button>
                        </form>
                    @elseif($order->status == 'pending')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Ce paiement est en attente de confirmation.
                        </div>
                        <form action="{{ route('admin.orders.status.update', $order) }}" method="POST" class="mb-3">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="paid">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-check"></i> Marquer comme payé
                            </button>
                        </form>
                        <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="failed">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times"></i> Marquer comme échoué
                            </button>
                        </form>
                    @elseif($order->status == 'refunded')
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Ce paiement a déjà été remboursé.
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> Ce paiement a échoué ou a été annulé.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

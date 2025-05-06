@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Détails de la Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i> Retour aux commandes
            </a>
        </div>
    </div>

    @if(session('admin_success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('admin_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Détails de la commande -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="h5 mb-0">Informations de la commande</h3>
                    <span class="badge {{ $order->status == 'paid' ? 'bg-success' : ($order->status == 'pending' ? 'bg-warning text-dark' : ($order->status == 'processing' ? 'bg-info text-dark' : ($order->status == 'refunded' ? 'bg-secondary' : 'bg-danger'))) }}">
                        {{ $order->status == 'paid' ? 'Payée' : ($order->status == 'pending' ? 'En attente' : ($order->status == 'processing' ? 'En traitement' : ($order->status == 'refunded' ? 'Remboursée' : 'Échouée'))) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="text-muted mb-1">Numéro de commande</p>
                            <p class="fw-bold">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">Date de commande</p>
                            <p>{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="text-muted mb-1">Méthode de paiement</p>
                            <p>{{ $order->payment_method ?? 'Orange Money' }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th>Type</th>
                                    <th>Prix unitaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->item_type == 'App\\Models\\Album' && $item->item->hasCoverImage())
                                                    <img src="{{ $item->item->getCoverImageUrl() }}" 
                                                         alt="{{ $item->item->title }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" 
                                                         class="me-3">
                                                @elseif($item->item_type == 'App\\Models\\Song' && $item->item->album && $item->item->album->hasCoverImage())
                                                    <img src="{{ $item->item->album->getCoverImageUrl() }}" 
                                                         alt="{{ $item->item->title }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover;" 
                                                         class="me-3">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-3"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas {{ $item->item_type == 'App\\Models\\Album' ? 'fa-compact-disc' : 'fa-music' }} text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $item->item->title }}</h6>
                                                    <small class="text-muted">
                                                        @if($item->item_type == 'App\\Models\\Song' && $item->item->album)
                                                            Album: {{ $item->item->album->title }}
                                                        @elseif($item->item_type == 'App\\Models\\Song')
                                                            Artiste: {{ $item->item->artist }}
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->item_type == 'App\\Models\\Album' ? 'Album' : 'Chanson' }}</td>
                                        <td>{{ number_format($item->price, 2) }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="text-end fw-bold">Total</td>
                                    <td class="fw-bold">{{ number_format($order->total_price, 2) }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Historique des changements de statut -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Historique des changements de statut</h3>
                </div>
                <div class="card-body">
                    @if(count($statusHistory) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Ancien statut</th>
                                        <th>Nouveau statut</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($statusHistory as $history)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="badge {{ $history->old_status == 'paid' ? 'bg-success' : ($history->old_status == 'pending' ? 'bg-warning text-dark' : ($history->old_status == 'processing' ? 'bg-info text-dark' : ($history->old_status == 'refunded' ? 'bg-secondary' : 'bg-danger'))) }}">
                                                    {{ $history->old_status }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $history->new_status == 'paid' ? 'bg-success' : ($history->new_status == 'pending' ? 'bg-warning text-dark' : ($history->new_status == 'processing' ? 'bg-info text-dark' : ($history->new_status == 'refunded' ? 'bg-secondary' : 'bg-danger'))) }}">
                                                    {{ $history->new_status }}
                                                </span>
                                            </td>
                                            <td>{{ $history->note ?: 'Aucune note' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i> Aucun historique de changement de statut disponible pour cette commande.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations client et actions -->
        <div class="col-lg-4">
            <!-- Information client -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Information client</h3>
                </div>
                <div class="card-body">
                    @if($order->user)
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <span class="text-white h5 mb-0">{{ substr($order->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $order->user->name }}</h5>
                                <p class="text-muted mb-0">{{ $order->user->email }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <p class="text-muted mb-1">Client depuis</p>
                            <p>{{ $order->user->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-muted mb-1">Total des commandes</p>
                            <p>{{ $order->user->orders()->count() }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> Le compte client associé à cette commande a été supprimé.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Modifier le statut -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Modifier le statut</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.status.update', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="form-label">Nouveau statut</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>En traitement</option>
                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Payée</option>
                                <option value="failed" {{ $order->status == 'failed' ? 'selected' : '' }}>Échouée</option>
                                <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Remboursée</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Note (optionnelle)</label>
                            <textarea class="form-control" id="note" name="note" rows="3" placeholder="Ajouter une note concernant ce changement de statut"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Mettre à jour le statut
                        </button>
                    </form>
                </div>
            </div>

            <!-- Commandes précédentes -->
            @if($previousOrders->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Commandes précédentes</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($previousOrders as $prevOrder)
                                <li class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Commande #{{ str_pad($prevOrder->id, 6, '0', STR_PAD_LEFT) }}</h6>
                                            <small class="text-muted">{{ $prevOrder->created_at->format('d/m/Y') }}</small>
                                        </div>
                                        <div>
                                            <span class="badge {{ $prevOrder->status == 'paid' ? 'bg-success' : ($prevOrder->status == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }} me-2">
                                                {{ number_format($prevOrder->total_price, 2) }} €
                                            </span>
                                            <a href="{{ route('admin.orders.show', $prevOrder) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Mes commandes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Détails de la commande -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h2 class="h4 mb-0">Détails de la commande</h2>
                    <span class="badge {{ $order->status == 'paid' ? 'bg-success' : ($order->status == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ $order->status == 'paid' ? 'Payée' : ($order->status == 'pending' ? 'En attente de paiement' : 'Paiement échoué') }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
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
                                    <th class="text-end">Prix</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->item_type == 'album')
                                                    @php
                                                        $album = \App\Models\Album::find($item->item_id);
                                                    @endphp
                                                    @if($album)
                                                        <div class="me-3">
                                                            @if($album->getFirstMedia('cover'))
                                                                <img src="{{ $album->getFirstMediaUrl('cover') }}" alt="{{ $album->title }}" style="width: 50px; height: 50px;" class="rounded shadow-sm">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-compact-disc text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $album->title }}</h6>
                                                            <div class="small text-muted">Album • {{ $album->artist }}</div>
                                                        </div>
                                                    @else
                                                        <div>Album non disponible</div>
                                                    @endif
                                                @elseif($item->item_type == 'song')
                                                    @php
                                                        $song = \App\Models\Song::find($item->item_id);
                                                    @endphp
                                                    @if($song)
                                                        <div class="me-3">
                                                            @if($song->album && $song->album->getFirstMedia('cover'))
                                                                <img src="{{ $song->album->getFirstMediaUrl('cover') }}" alt="{{ $song->title }}" style="width: 50px; height: 50px;" class="rounded shadow-sm">
                                                            @else
                                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                                    <i class="fas fa-music text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $song->title }}</h6>
                                                            <div class="small text-muted">
                                                                Chanson
                                                                @if($song->album)
                                                                    • Album: {{ $song->album->title }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div>Chanson non disponible</div>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end">{{ number_format($item->price, 2) }} €</td>
                                        <td class="text-center">
                                            @if($order->status == 'paid')
                                                @if($item->item_type == 'album')
                                                    @php
                                                        $album = \App\Models\Album::find($item->item_id);
                                                    @endphp
                                                    @if($album)
                                                        <a href="{{ route('albums.show', $album) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i> Voir
                                                        </a>
                                                    @endif
                                                @elseif($item->item_type == 'song')
                                                    @php
                                                        $song = \App\Models\Song::find($item->item_id);
                                                    @endphp
                                                    @if($song)
                                                        <a href="{{ route('stream.play', $song) }}" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-play me-1"></i> Écouter
                                                        </a>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="badge bg-warning text-dark">En attente de paiement</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-bold">Total</td>
                                    <td class="text-end fw-bold">{{ number_format($order->total_price, 2) }} €</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour à mes commandes
            </a>
        </div>

        <!-- Informations de paiement -->
        <div class="col-lg-4">
            <!-- Statut de la commande -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Statut de la commande</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Commande passée</span>
                            <i class="fas fa-check-circle text-success"></i>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Paiement</span>
                            @if($order->status == 'paid')
                                <i class="fas fa-check-circle text-success"></i>
                            @elseif($order->status == 'pending')
                                <i class="fas fa-clock text-warning"></i>
                            @else
                                <i class="fas fa-times-circle text-danger"></i>
                            @endif
                        </li>
                    </ul>

                    @if($order->status == 'pending')
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('payments.show', $order) }}" class="btn btn-primary">
                                <i class="fas fa-credit-card me-2"></i> Procéder au paiement
                            </a>
                        </div>
                    @elseif($order->status == 'paid')
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-2"></i> Votre paiement a été confirmé. Vous pouvez maintenant accéder à votre contenu.
                        </div>
                    @else
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-times-circle me-2"></i> Votre paiement a échoué. Veuillez réessayer.
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('payments.show', $order) }}" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i> Réessayer le paiement
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaction -->
            @if($order->transaction_id)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Détails de la transaction</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>ID de transaction</span>
                                <span class="text-muted">{{ $order->transaction_id }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Méthode</span>
                                <span class="text-muted">{{ $order->payment_method ?? 'Orange Money' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>Date</span>
                                <span class="text-muted">{{ $order->updated_at->format('d/m/Y H:i') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

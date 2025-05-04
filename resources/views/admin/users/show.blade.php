@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="fas fa-user me-2"></i>Détails de l'Utilisateur
    </h2>
    <div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
        </a>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Modifier
        </a>
    </div>
</div>

@if(session('admin_success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i>{{ session('admin_success') }}
</div>
@endif

@if(session('admin_error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('admin_error') }}
</div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Informations utilisateur</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 150px;">ID</th>
                        <td>{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <th>Nom</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Statut</th>
                        <td>
                            @if($user->is_admin)
                                <span class="badge bg-primary">Administrateur</span>
                            @else
                                <span class="badge bg-secondary">Client</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Inscription</th>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Dernière MAJ</th>
                        <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Statistiques</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3 border rounded bg-light">
                            <h4 class="h2 mb-0">{{ $orders->count() }}</h4>
                            <p class="text-muted mb-0">Commandes</p>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 border rounded bg-light">
                            <h4 class="h2 mb-0">{{ $playlists->count() }}</h4>
                            <p class="text-muted mb-0">Playlists</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dernières commandes -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h3 class="h5 mb-0">Dernières commandes</h3>
    </div>
    <div class="card-body p-0">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($order->total_price, 2) }} €</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge bg-warning">En attente</span>
                                    @elseif($order->status == 'completed')
                                        <span class="badge bg-success">Complétée</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge bg-danger">Annulée</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(Route::has('admin.orders.show'))
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Aucune commande trouvée pour cet utilisateur.</p>
            </div>
        @endif
    </div>
</div>

<!-- Playlists -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h3 class="h5 mb-0">Playlists</h3>
    </div>
    <div class="card-body p-0">
        @if($playlists->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Titres</th>
                            <th>Créée le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($playlists as $playlist)
                            <tr>
                                <td>{{ $playlist->name }}</td>
                                <td>{{ $playlist->songs_count }}</td>
                                <td>{{ $playlist->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if(Route::has('admin.playlists.show'))
                                        <a href="{{ route('admin.playlists.show', $playlist) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="fas fa-music fa-3x mb-3"></i>
                <p>Aucune playlist trouvée pour cet utilisateur.</p>
            </div>
        @endif
    </div>
</div>

<!-- Bouton de suppression -->
<div class="card shadow-sm mb-4 border-danger">
    <div class="card-header bg-danger text-white">
        <h3 class="h5 mb-0">Zone de danger</h3>
    </div>
    <div class="card-body">
        <p class="mb-3">La suppression d'un compte utilisateur est irréversible et entraînera la perte de toutes les données associées.</p>
        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Êtes-vous vraiment sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i>Supprimer cet utilisateur
            </button>
        </form>
    </div>
</div>
@endsection

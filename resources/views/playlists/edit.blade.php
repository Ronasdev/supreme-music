@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('playlists.index') }}">Mes playlists</a></li>
            <li class="breadcrumb-item"><a href="{{ route('playlists.show', $playlist) }}">{{ $playlist->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h1 class="h4 mb-0">Modifier la playlist</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('playlists.update', $playlist) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la playlist <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $playlist->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">Description (optionnel)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $playlist->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer les modifications
                            </button>
                            <a href="{{ route('playlists.show', $playlist) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Section pour gérer les chansons de la playlist -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h2 class="h4 mb-0">Gérer les chansons</h2>
                </div>
                <div class="card-body">
                    @if($playlist->songs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px">#</th>
                                        <th>Titre</th>
                                        <th>Artiste</th>
                                        <th style="width: 100px"></th>
                                    </tr>
                                </thead>
                                <tbody id="playlist-songs">
                                    @foreach($playlist->songs as $index => $song)
                                        <tr>
                                            <td class="text-muted">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($song->album && $song->album->getFirstMedia('cover'))
                                                            <img src="{{ $song->album->getFirstMediaUrl('cover') }}" alt="{{ $song->title }}" style="width: 40px; height: 40px;" class="rounded shadow-sm">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                <i class="fas fa-music text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>{{ $song->title }}</div>
                                                </div>
                                            </td>
                                            <td>{{ $song->artist ?? 'Artiste inconnu' }}</td>
                                            <td>
                                                <form action="{{ route('playlists.remove-song', [$playlist, $song]) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer de la playlist">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-music fa-3x text-muted mb-3"></i>
                            <h5>Cette playlist est vide</h5>
                            <p>Ajoutez des chansons à partir du catalogue.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('catalog') }}" class="btn btn-primary w-100">
                        <i class="fas fa-plus me-2"></i> Ajouter des chansons
                    </a>
                </div>
            </div>
            
            <!-- Section de danger -->
            <div class="card border-danger mt-4">
                <div class="card-header bg-danger text-white">
                    <h3 class="h5 mb-0">Zone de danger</h3>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Supprimer cette playlist</h5>
                    <p class="card-text">La suppression d'une playlist est définitive et irréversible. Les chansons qu'elle contient ne seront pas supprimées de votre bibliothèque.</p>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePlaylistModal">
                        <i class="fas fa-trash-alt me-2"></i> Supprimer cette playlist
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression de playlist -->
<div class="modal fade" id="deletePlaylistModal" tabindex="-1" aria-labelledby="deletePlaylistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePlaylistModalLabel">Supprimer la playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la playlist <strong>{{ $playlist->name }}</strong> ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('playlists.destroy', $playlist) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

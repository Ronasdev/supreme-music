@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('playlists.index') }}">Mes playlists</a></li>
            <li class="breadcrumb-item active" aria-current="page">Créer une playlist</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h1 class="h4 mb-0">Créer une nouvelle playlist</h1>
                </div>
                <div class="card-body">
                    <form action="{{ route('playlists.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom de la playlist <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">Description (optionnel)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Décrivez votre playlist en quelques mots (style musical, ambiance, occasion...)</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Créer la playlist
                            </button>
                            <a href="{{ route('playlists.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-lightbulb text-warning me-2"></i> Conseils pour créer une playlist</h5>
                    <ul class="mb-0">
                        <li>Donnez un nom évocateur à votre playlist qui reflète son contenu ou son ambiance.</li>
                        <li>Utilisez la description pour préciser l'objectif de la playlist (ex: "Musique pour se détendre" ou "Mes morceaux préférés de jazz").</li>
                        <li>Après avoir créé la playlist, vous pourrez y ajouter des chansons depuis notre catalogue ou depuis les pages de chansons individuelles.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

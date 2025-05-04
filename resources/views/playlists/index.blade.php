@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Mes playlists</h1>
        <a href="{{ route('playlists.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i> Créer une playlist
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(count($playlists) > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($playlists as $playlist)
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <!-- Image de couverture -->
                        <div class="position-relative">
                            @if($playlist->songs->count() > 0 && $playlist->songs->first()->album && $playlist->songs->first()->album->getFirstMedia('cover'))
                                <img src="{{ $playlist->songs->first()->album->getFirstMediaUrl('cover') }}" class="card-img-top" alt="{{ $playlist->name }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-light text-center py-5" style="height: 180px;">
                                    <i class="fas fa-music fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 start-0 end-0 p-3 bg-gradient-dark text-white">
                                <h5 class="card-title mb-0">{{ $playlist->name }}</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Statistiques -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span><i class="fas fa-music me-2"></i> {{ $playlist->songs->count() }} chansons</span>
                                <small class="text-muted">Créée le {{ $playlist->created_at->format('d/m/Y') }}</small>
                            </div>
                            
                            <!-- Description -->
                            @if($playlist->description)
                                <p class="card-text small mb-3">{{ \Illuminate\Support\Str::limit($playlist->description, 100) }}</p>
                            @endif
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between">
                            <a href="{{ route('playlists.show', $playlist) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-headphones me-1"></i> Écouter
                            </a>
                            <div>
                                <a href="{{ route('playlists.edit', $playlist) }}" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('playlists.destroy', $playlist) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette playlist ?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-music fa-5x text-muted"></i>
                </div>
                <h2 class="mb-3">Vous n'avez pas encore de playlist</h2>
                <p class="lead mb-4">Créez une playlist pour organiser vos chansons préférées.</p>
                <a href="{{ route('playlists.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Créer ma première playlist
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

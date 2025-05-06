@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('playlists.index') }}">Mes playlists</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $playlist->name }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- En-tête de la playlist -->
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <div class="row">
                        <!-- Image/Couverture -->
                        <div class="col-md-4 mb-3 mb-md-0">
                            @if($playlist->songs->count() > 0 && $playlist->songs->first()->album && $playlist->songs->first()->album->hasCoverImage())
                                <img src="{{ $playlist->songs->first()->album->getCoverImageUrl() }}" class="img-fluid rounded shadow" alt="{{ $playlist->name }}">
                            @else
                                <div class="bg-light text-center rounded shadow py-5">
                                    <i class="fas fa-music fa-4x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Détails -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h1 class="h2 mb-0">{{ $playlist->name }}</h1>
                                <div class="dropdown">
                                    <button class="btn btn-light rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('playlists.edit', $playlist) }}">
                                                <i class="fas fa-edit me-2"></i> Modifier
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deletePlaylistModal">
                                                <i class="fas fa-trash-alt me-2"></i> Supprimer
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            
                            <p class="text-muted mb-3">
                                Créée par {{ $playlist->user->name }} • {{ $playlist->created_at->format('d/m/Y') }}
                            </p>
                            
                            @if($playlist->description)
                                <p class="mb-3">{{ $playlist->description }}</p>
                            @endif
                            
                            <div class="d-flex align-items-center mb-3">
                                <span class="me-3"><i class="fas fa-music me-2"></i> {{ $playlist->songs->count() }} chansons</span>
                                <span><i class="fas fa-clock me-2"></i> {{ $playlistDuration ?? '00:00' }}</span>
                            </div>
                            
                            <div class="d-flex">
                                @if($playlist->songs->count() > 0)
                                    <button id="playAllBtn" class="btn btn-primary me-2">
                                        <i class="fas fa-play me-2"></i> Lire tout
                                    </button>
                                @endif
                                <a href="{{ route('playlists.edit', $playlist) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-edit me-2"></i> Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Liste des chansons -->
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="h5 mb-0">Chansons</h3>
                    @if($playlist->songs->count() > 0)
                        <div class="d-flex align-items-center">
                            <div class="dropdown me-2">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-sort me-1"></i> Trier
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#">Titre (A-Z)</a></li>
                                    <li><a class="dropdown-item" href="#">Titre (Z-A)</a></li>
                                    <li><a class="dropdown-item" href="#">Artiste (A-Z)</a></li>
                                    <li><a class="dropdown-item" href="#">Album</a></li>
                                    <li><a class="dropdown-item" href="#">Date d'ajout (plus récent)</a></li>
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="card-body p-0">
                    @if($playlist->songs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px">#</th>
                                        <th>Titre</th>
                                        <th>Album</th>
                                        <th style="width: 80px" class="text-center">Durée</th>
                                        <th style="width: 100px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($playlist->songs as $index => $song)
                                        <tr data-song-id="{{ $song->id }}">
                                            <td class="text-muted text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($song->album && $song->album->hasCoverImage())
                                                            <img src="{{ $song->album->getCoverImageUrl() }}" alt="{{ $song->title }}" style="width: 40px; height: 40px;" class="rounded shadow-sm">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                                <i class="fas fa-music text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $song->title }}</h6>
                                                        <div class="small text-muted">{{ $song->artist ?? 'Artiste inconnu' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($song->album)
                                                    <a href="{{ route('albums.show', $song->album) }}" class="text-decoration-none">{{ $song->album->title }}</a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $song->duration ?? '3:45' }}</td>
                                            <td>
                                                <div class="d-flex justify-content-end">
                                                    <a href="{{ route('stream.play', $song) }}" class="btn btn-sm btn-outline-primary me-1" title="Écouter">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                    <form action="{{ route('playlists.remove-song', [$playlist, $song]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer de la playlist">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-music fa-3x text-muted mb-3"></i>
                            <h5>Cette playlist est vide</h5>
                            <p class="mb-4">Ajoutez des chansons à partir du catalogue.</p>
                            <a href="{{ route('catalog') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i> Parcourir le catalogue
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Recommandations -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Recommandé pour vous</h3>
                </div>
                <div class="card-body p-0">
                    @if(count($recommendedSongs ?? []) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recommendedSongs as $recommendedSong)
                                <div class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($recommendedSong->album && $recommendedSong->album->hasCoverImage())
                                                <img src="{{ $recommendedSong->album->getCoverImageUrl() }}" alt="{{ $recommendedSong->title }}" style="width: 40px; height: 40px;" class="rounded shadow-sm">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-music text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $recommendedSong->title }}</h6>
                                            <div class="small text-muted">{{ $recommendedSong->artist ?? 'Artiste inconnu' }}</div>
                                        </div>
                                        <div>
                                            <form action="{{ route('playlists.add-song-to', [$playlist, $recommendedSong]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Ajouter à la playlist">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Aucune recommandation disponible pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Autres playlists -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="h5 mb-0">Vos autres playlists</h3>
                </div>
                <div class="card-body p-0">
                    @if(count($otherPlaylists ?? []) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($otherPlaylists as $otherPlaylist)
                                <a href="{{ route('playlists.show', $otherPlaylist) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($otherPlaylist->songs->count() > 0 && $otherPlaylist->songs->first()->album && $otherPlaylist->songs->first()->album->hasCoverImage())
                                                <img src="{{ $otherPlaylist->songs->first()->album->getCoverImageUrl() }}" alt="{{ $otherPlaylist->name }}" style="width: 40px; height: 40px;" class="rounded shadow-sm">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="fas fa-music text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $otherPlaylist->name }}</h6>
                                            <div class="small text-muted">{{ $otherPlaylist->songs->count() }} chansons</div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted mb-0">Vous n'avez pas d'autres playlists.</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <a href="{{ route('playlists.create') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-plus me-1"></i> Créer une nouvelle playlist
                    </a>
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
                <p>Êtes-vous sûr de vouloir supprimer cette playlist ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('playlists.destroy', $playlist) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const playAllBtn = document.getElementById('playAllBtn');
        
        if (playAllBtn) {
            playAllBtn.addEventListener('click', function() {
                const firstSongId = document.querySelector('tbody tr').dataset.songId;
                if (firstSongId) {
                    window.location.href = "{{ route('stream.play', '') }}/" + firstSongId;
                }
            });
        }
    });
</script>
@endsection

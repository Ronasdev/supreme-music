@extends('layouts.app')

@section('title', 'Ma bibliothèque musicale')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Sidebar - Playlists -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Mes playlists</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('library') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-music me-2"></i> Tous mes morceaux
                        <span class="badge bg-primary rounded-pill float-end">{{ $songs->total() }}</span>
                    </a>
                    
                    @forelse($playlists as $playlist)
                        <a href="{{ route('playlists.show', $playlist) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-list me-2"></i> {{ $playlist->name }}
                            </div>
                            <span class="badge bg-secondary rounded-pill">{{ $playlist->songs_count }}</span>
                        </a>
                    @empty
                        <div class="list-group-item text-muted">
                            <p class="mb-1">Vous n'avez pas encore créé de playlist.</p>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer">
                    <a href="{{ route('playlists.create') }}" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-plus me-2"></i> Créer une playlist
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content - Songs -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Ma bibliothèque musicale</h2>
                
                @if($songs->count() > 0)
                    <div class="btn-group">
                        <button class="btn btn-outline-primary" id="playAllBtn">
                            <i class="fas fa-play me-2"></i> Tout lire
                        </button>
                        <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-mdb-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Options</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" id="shuffleAllBtn">
                                    <i class="fas fa-random me-2"></i> Lecture aléatoire
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
            
            <!-- Alert messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Songs List -->
            @if($songs->count() > 0)
                <div class="card shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Titre</th>
                                    <th>Album</th>
                                    <th>Durée</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($songs as $index => $song)
                                    <tr>
                                        <td class="align-middle">{{ $index + 1 + ($songs->currentPage() - 1) * $songs->perPage() }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                @if($song->album && $song->album->hasCoverImage())
                                                    <img src="{{ $song->album->getCoverImageUrl() }}" 
                                                         class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;" 
                                                         alt="{{ $song->album->title }}">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-music text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $song->title }}</h6>
                                                    <small class="text-muted">{{ $song->artist }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            @if($song->album)
                                                <a href="{{ route('albums.show', $song->album) }}" class="text-decoration-none">
                                                    {{ $song->album->title }}
                                                </a>
                                            @else
                                                <span class="text-muted">Single</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">{{ gmdate("i:s", $song->duration ?? 0) }}</td>
                                        <td class="align-middle">
                                            <div class="btn-group">
                                                <a href="{{ route('stream.play', $song) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" 
                                                        data-mdb-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Options</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('stream.download', $song) }}">
                                                            <i class="fas fa-download me-2"></i> Télécharger
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item add-to-playlist" data-song-id="{{ $song->id }}">
                                                            <i class="fas fa-plus me-2"></i> Ajouter à une playlist
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $songs->links() }}
                </div>
            @else
                <div class="card shadow-sm">
                    <div class="card-body p-5 text-center">
                        <div class="py-4">
                            <i class="fas fa-music fa-4x text-muted mb-4"></i>
                            <h4>Votre bibliothèque est vide</h4>
                            <p class="text-muted mb-4">Vous n'avez pas encore acheté de morceaux ou d'albums.</p>
                            <a href="{{ route('albums.index') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i> Découvrir de la musique
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal: Add to Playlist -->
<div class="modal fade" id="addToPlaylistModal" tabindex="-1" aria-labelledby="addToPlaylistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToPlaylistModalLabel">Ajouter à une playlist</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addToPlaylistForm" method="POST">
                    @csrf
                    <input type="hidden" id="songIdInput" name="song_id" value="">
                    
                    <div class="form-outline mb-4">
                        <select class="form-select" id="playlistSelect" name="playlist_id" required>
                            <option value="" selected disabled>Choisissez une playlist</option>
                            @foreach($playlists as $playlist)
                                <option value="{{ $playlist->id }}">{{ $playlist->name }} ({{ $playlist->songs_count }} morceaux)</option>
                            @endforeach
                        </select>
                        <label class="form-label" for="playlistSelect">Playlist</label>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-outline-secondary" data-mdb-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
                
                <div class="mt-4 text-center">
                    <p class="mb-2">ou créez une nouvelle playlist</p>
                    <a href="{{ route('playlists.create') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus me-2"></i> Nouvelle playlist
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle "Add to Playlist" buttons
        const addToPlaylistButtons = document.querySelectorAll('.add-to-playlist');
        const songIdInput = document.getElementById('songIdInput');
        const addToPlaylistForm = document.getElementById('addToPlaylistForm');
        
        addToPlaylistButtons.forEach(button => {
            button.addEventListener('click', function() {
                const songId = this.getAttribute('data-song-id');
                songIdInput.value = songId;
                
                // Set the form action dynamically
                addToPlaylistForm.action = `/playlists/${document.getElementById('playlistSelect').value}/songs`;
                
                // Show the modal
                const modal = new mdb.Modal(document.getElementById('addToPlaylistModal'));
                modal.show();
            });
        });
        
        // Update form action when playlist selection changes
        document.getElementById('playlistSelect').addEventListener('change', function() {
            addToPlaylistForm.action = `/playlists/${this.value}/songs`;
        });
    });
</script>
@endsection

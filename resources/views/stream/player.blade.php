@extends('layouts.app')

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            @if($song->album)
                <li class="breadcrumb-item"><a href="{{ route('albums.show', $song->album) }}">{{ $song->album->title }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $song->title }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Lecteur audio principal -->
            <div class="card shadow-lg mb-4">
                <div class="card-body p-0">
                    <!-- Image de couverture -->
                    <div class="position-relative">
                        @if($song->album && $song->album->hasCoverImage())
                            <img src="{{ $song->album->getCoverImageUrl() }}" class="card-img-top" alt="{{ $song->title }}">
                        @else
                            <div class="bg-light text-center py-5">
                                <i class="fas fa-music fa-5x text-muted"></i>
                            </div>
                        @endif
                        <div class="position-absolute top-0 end-0 m-3">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle shadow" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addToPlaylistModal">
                                            <i class="fas fa-plus me-2"></i> Ajouter à une playlist
                                        </a>
                                    </li>
                                    @if($song->album)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('albums.show', $song->album) }}">
                                                <i class="fas fa-compact-disc me-2"></i> Voir l'album
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Informations sur la chanson -->
                    <div class="p-4">
                        <h2 class="mb-1">{{ $song->title }}</h2>
                        <p class="text-muted mb-4">
                            {{ $song->artist ?? ($song->album ? $song->album->artist : 'Artiste inconnu') }}
                            @if($song->album)
                                • {{ $song->album->title }}
                            @endif
                            @if($song->year)
                                • {{ $song->year }}
                            @endif
                        </p>

                        <!-- Lecteur audio HTML5 -->
                        <div class="mb-4">
                            <audio id="audioPlayer" class="w-100" controls controlsList="nodownload">
                                <source src="{{ $audioUrl }}" type="audio/mpeg">
                                Votre navigateur ne prend pas en charge la lecture audio.
                            </audio>
                        </div>

                        <!-- Contrôles du lecteur avancés -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <button id="prevBtn" class="btn btn-outline-secondary me-2" disabled>
                                    <i class="fas fa-step-backward"></i>
                                </button>
                                <button id="playPauseBtn" class="btn btn-primary me-2">
                                    <i class="fas fa-play"></i>
                                </button>
                                <button id="nextBtn" class="btn btn-outline-secondary">
                                    <i class="fas fa-step-forward"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center">
                                <button id="volumeBtn" class="btn btn-outline-secondary me-2">
                                    <i class="fas fa-volume-up"></i>
                                </button>
                                <div class="volume-slider me-2" style="width: 100px; display: none;">
                                    <input type="range" class="form-range" id="volumeSlider" min="0" max="1" step="0.1" value="1">
                                </div>
                                <button id="repeatBtn" class="btn btn-outline-secondary">
                                    <i class="fas fa-repeat"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Metadata de la chanson -->
                        @if($song->description)
                            <div class="mt-4">
                                <h5>À propos de cette chanson</h5>
                                <p>{{ $song->description }}</p>
                            </div>
                        @endif

                        <!-- Partage et boutons sociaux -->
                        <div class="mt-4 d-flex">
                            <button class="btn btn-outline-primary me-2">
                                <i class="fas fa-share-alt me-1"></i> Partager
                            </button>
                            <button class="btn btn-outline-danger">
                                <i class="fas fa-heart me-1"></i> J'aime
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Autres chansons de l'album -->
            @if($song->album && $song->album->songs->count() > 1)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Autres chansons de l'album</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($song->album->songs as $albumSong)
                                @if($albumSong->id != $song->id)
                                    <a href="{{ route('stream.play', $albumSong) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 text-muted">{{ $loop->iteration }}</div>
                                                <div>
                                                    <h6 class="mb-0">{{ $albumSong->title }}</h6>
                                                    <small class="text-muted">{{ $albumSong->duration ?? '3:45' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            @if(Auth::check() && Auth::user()->hasPurchased($albumSong))
                                                <i class="fas fa-play text-primary"></i>
                                            @else
                                                <span class="badge bg-primary">{{ number_format($albumSong->price, 2) }} €</span>
                                            @endif
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Playlists de l'utilisateur -->
            @if(Auth::check() && Auth::user()->playlists->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h3 class="h5 mb-0">Vos playlists</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach(Auth::user()->playlists as $playlist)
                                <a href="{{ route('playlists.show', $playlist) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-music text-muted"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $playlist->name }}</h6>
                                            <small class="text-muted">{{ $playlist->songs->count() }} chansons</small>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="{{ route('playlists.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-list me-1"></i> Voir toutes mes playlists
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal d'ajout à une playlist -->
<div class="modal fade" id="addToPlaylistModal" tabindex="-1" aria-labelledby="addToPlaylistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addToPlaylistModalLabel">Ajouter à une playlist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(Auth::check())
                    @if(Auth::user()->playlists->count() > 0)
                        <form action="{{ route('playlists.add-song', $song) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="playlist_id" class="form-label">Sélectionnez une playlist</label>
                                <select class="form-select" id="playlist_id" name="playlist_id" required>
                                    @foreach(Auth::user()->playlists as $playlist)
                                        <option value="{{ $playlist->id }}">{{ $playlist->name }} ({{ $playlist->songs->count() }} chansons)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i> Ajouter
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center my-4">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <p>Vous n'avez pas encore créé de playlist.</p>
                            <a href="{{ route('playlists.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Créer une playlist
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center my-4">
                        <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                        <p>Vous devez être connecté pour ajouter des chansons à une playlist.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i> Se connecter
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const audioPlayer = document.getElementById('audioPlayer');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const volumeBtn = document.getElementById('volumeBtn');
        const volumeSlider = document.querySelector('.volume-slider');
        const volumeControl = document.getElementById('volumeSlider');
        const repeatBtn = document.getElementById('repeatBtn');
        
        // Play/Pause button
        playPauseBtn.addEventListener('click', function() {
            if (audioPlayer.paused) {
                audioPlayer.play();
                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            } else {
                audioPlayer.pause();
                playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            }
        });
        
        // Audio ended event
        audioPlayer.addEventListener('ended', function() {
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
            
            if (repeatBtn.classList.contains('active')) {
                audioPlayer.currentTime = 0;
                audioPlayer.play();
                playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            }
        });
        
        // Volume control
        volumeBtn.addEventListener('click', function() {
            volumeSlider.style.display = volumeSlider.style.display === 'none' ? 'block' : 'none';
        });
        
        volumeControl.addEventListener('input', function() {
            audioPlayer.volume = this.value;
            
            if (this.value == 0) {
                volumeBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
            } else if (this.value < 0.5) {
                volumeBtn.innerHTML = '<i class="fas fa-volume-down"></i>';
            } else {
                volumeBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
            }
        });
        
        // Repeat button
        repeatBtn.addEventListener('click', function() {
            this.classList.toggle('active');
            if (this.classList.contains('active')) {
                this.classList.add('btn-primary');
                this.classList.remove('btn-outline-secondary');
            } else {
                this.classList.remove('btn-primary');
                this.classList.add('btn-outline-secondary');
            }
        });
        
        // Initialize player state
        audioPlayer.addEventListener('play', function() {
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        });
        
        audioPlayer.addEventListener('pause', function() {
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        });
    });
</script>
@endsection

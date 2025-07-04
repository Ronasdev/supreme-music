@extends('layouts.app')

@section('title', $song->title)

@section('content')
<div class="container my-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            @if($song->album)
                <li class="breadcrumb-item"><a href="{{ route('albums.index') }}">Albums</a></li>
                <li class="breadcrumb-item"><a href="{{ route('albums.show', $song->album) }}">{{ $song->album->title }}</a></li>
            @else
                <li class="breadcrumb-item"><a href="{{ route('songs.index') }}">Chansons</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $song->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Image de la chanson (cover de l'album) -->
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="position-relative">
                @if($song->album && $song->album->hasCoverImage())
                    <img src="{{ $song->album->getCoverImageUrl() }}" alt="{{ $song->title }}" class="img-fluid rounded shadow-lg">
                @else
                    <div class="rounded shadow-lg bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <i class="fas fa-music fa-5x text-muted"></i>
                    </div>
                @endif
                <div class="position-absolute top-0 end-0 p-3">
                    <span class="badge bg-primary rounded-pill fs-5">{{ number_format($song->price, 2) }} €</span>
                </div>
            </div>
        </div>

        <!-- Informations sur la chanson -->
        <div class="col-lg-8">
            <h1 class="fw-bold mb-3">{{ $song->title }}</h1>
            
            <div class="d-flex align-items-center mb-3">
                @if($song->album)
                    <span class="badge bg-info text-dark me-2">Album: <a href="{{ route('albums.show', $song->album) }}" class="text-decoration-none text-dark">{{ $song->album->title }}</a></span>
                @else
                    <span class="badge bg-info text-dark me-2">Single</span>
                @endif
                
                @if($song->duration)
                    <span class="badge bg-light text-dark">Durée: {{ $song->duration }}</span>
                @endif
            </div>
            
            <div class="mb-4">
                <h5 class="fw-bold">Description</h5>
                <p class="lead">{{ $song->description ?? 'Aucune description disponible pour cette chanson.' }}</p>
            </div>
            
            <!-- Lecteur Audio (si l'utilisateur a acheté la chanson) -->
            @auth
                @if(Auth::user()->orders()->whereHas('orderItems', function($query) use ($song) {
                    $query->where('item_id', $song->id)->where('item_type', 'App\\Models\\Song');
                })->where('status', 'paid')->exists())
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Écouter maintenant</h5>
                            <audio controls class="w-100 mt-2" id="audioPlayer">
                                <source src="{{ route('stream.play', $song) }}" type="audio/mpeg">
                                Votre navigateur ne supporte pas la lecture audio.
                            </audio>
                        </div>
                    </div>
                    
                    <!-- Ajouter à une playlist -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Ajouter à une playlist</h5>
                            @php
                                $playlists = Auth::user()->playlists;
                            @endphp
                            
                            @if(count($playlists) > 0)
                                <form action="{{ route('playlists.songs.add', $playlists[0]) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="song_id" value="{{ $song->id }}">
                                    <select name="playlist_id" class="form-select">
                                        @foreach($playlists as $playlist)
                                            <option value="{{ $playlist->id }}">{{ $playlist->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                </form>
                            @else
                                <p class="mb-2">Vous n'avez pas encore de playlist.</p>
                                <a href="{{ route('playlists.create') }}" class="btn btn-sm btn-outline-primary">Créer une playlist</a>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Actions d'achat -->
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @if($isInCart($song->id, 'song'))
                            <a href="{{ route('cart.show') }}" class="btn btn-success">
                                <i class="fas fa-check me-2"></i> Déjà dans votre panier
                            </a>
                        @else
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $song->id }}">
                                <input type="hidden" name="type" value="song">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i> Ajouter au panier
                                </button>
                            </form>
                        @endif
                        
                        @if($song->album)
                            <a href="{{ route('albums.show', $song->album) }}" class="btn btn-outline-info">
                                <i class="fas fa-compact-disc me-2"></i> Voir l'album complet
                            </a>
                        @endif
                    </div>
                    <!-- Prévisualisation audio -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Écouter un extrait</h5>
                            <button id="previewButton" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-headphones me-2"></i> Prévisualiser
                            </button>
                            <div id="previewPlayer" class="mt-3" style="display: none;">
                                <audio controls class="w-100" id="previewAudio">
                                    Votre navigateur ne supporte pas la lecture audio.
                                </audio>
                                <div class="text-center mt-2">
                                    <small class="text-muted">Extrait de 30 secondes. Achetez pour écouter la version complète.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Utilisateur non connecté -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Connectez-vous pour acheter et écouter cette chanson.
                    <a href="{{ route('login') }}" class="btn btn-outline-primary ms-3">Se connecter</a>
                </div>
                
                <!-- Prévisualisation audio (accessible même sans compte) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Écouter un extrait</h5>
                        <button id="previewButton" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-headphones me-2"></i> Prévisualiser
                        </button>
                        <div id="previewPlayer" class="mt-3" style="display: none;">
                            <audio controls class="w-100" id="previewAudio">
                                Votre navigateur ne supporte pas la lecture audio.
                            </audio>
                            <div class="text-center mt-2">
                                <small class="text-muted">Extrait de 30 secondes. Créez un compte pour acheter et écouter la version complète.</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
            
            <!-- Bouton de retour -->
            <div class="mt-3">
                @if($song->album)
                    <a href="{{ route('albums.show', $song->album) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour à l'album
                    </a>
                @else
                    <a href="{{ route('songs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour aux chansons
                    </a>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recommandations -->
    <div class="mt-5">
        <h3 class="fw-bold mb-4">Titres similaires</h3>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($similarSongs as $relatedSong)
                <div class="col">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="position-relative">
                            @if($relatedSong->album && $relatedSong->album->hasCoverImage())
                                <img src="{{ $relatedSong->album->getCoverImageUrl() }}" class="card-img-top" alt="{{ $relatedSong->title }}">
                            @else
                                <div class="bg-light text-center py-5">
                                    <i class="fas fa-music fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $relatedSong->title }}</h5>
                            @if($relatedSong->album)
                                <p class="card-text text-muted">Album: {{ $relatedSong->album->title }}</p>
                            @else
                                <p class="card-text text-muted">Single</p>
                            @endif
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('songs.show', $relatedSong) }}" class="btn btn-sm btn-outline-primary w-100">Voir le titre</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease-in-out;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
    }
    
    #audioPlayer {
        height: 40px;
        border-radius: 20px;
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script pour le lecteur audio principal
        const player = document.getElementById('audioPlayer');
        if (player) {
            player.volume = 0.7; // Volume par défaut à 70%
        }
        
        // Gestion de la prévisualisation
        const previewButton = document.getElementById('previewButton');
        const previewPlayer = document.getElementById('previewPlayer');
        const previewAudio = document.getElementById('previewAudio');
        
        if (previewButton) {
            previewButton.addEventListener('click', function() {
                // Affiche le lecteur
                previewPlayer.style.display = 'block';
                previewButton.style.display = 'none';
                
                // Ajouter une classe visuelle d'attente
                document.body.classList.add('loading-preview');
                
                // Afficher un message de chargement
                const loadingMessage = document.createElement('div');
                loadingMessage.className = 'text-info mt-2';
                loadingMessage.id = 'preview-loading-message';
                loadingMessage.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Chargement de la prévisualisation en cours...';
                previewPlayer.appendChild(loadingMessage);
                
                // Fetch pour récupérer l'URL de prévisualisation
                fetch('{{ route("songs.preview", $song) }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur serveur: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Détails de l\'audio:', data);
                        
                        if (loadingMessage) {
                            loadingMessage.remove();
                        }
                        
                        if (data.preview_url) {
                            // Vérifier et afficher l'URL de prévisualisation dans la console pour débogage
                            console.log('URL de prévisualisation:', data.preview_url);
                            
                            // Ajouter un gestionnaire d'erreurs pour l'audio
                            previewAudio.onerror = function(e) {
                                console.error('Erreur de lecture audio:', e);
                                // Information d'erreur plus détaillée
                                const errorDetails = previewAudio.error ? 
                                    `Code: ${previewAudio.error.code}, Message: ${previewAudio.error.message}` : 
                                    'Erreur inconnue';
                                console.error('Détails de l\'erreur:', errorDetails);
                                alert('Erreur lors de la lecture du fichier audio. Consultez la console pour plus de détails.');
                            };
                            
                            // Configurer et lancer la lecture avec un délai pour s'assurer que le fichier est prêt
                            previewAudio.src = data.preview_url;
                            previewAudio.load();
                            
                            // Ajouter un gestionnaire pour la lecture réussie
                            previewAudio.oncanplay = function() {
                                console.log('Audio prêt à être joué');
                                
                                // Gestionnaire d'événement pour limiter la lecture à 30 secondes
                                if (data.preview_duration) {
                                    // S'assurer qu'il n'y a pas de duplication des gestionnaires
                                    previewAudio.removeEventListener('timeupdate', limitPlaybackDuration);
                                    
                                    // Créer une fonction pour limiter la durée
                                    function limitPlaybackDuration() {
                                        if (previewAudio.currentTime >= data.preview_duration) {
                                            console.log(`Limite de prévisualisation de ${data.preview_duration}s atteinte!`);
                                            previewAudio.pause();
                                            // Afficher un message d'information
                                            const previewLimitMessage = document.getElementById('preview-limit-message');
                                            if (!previewLimitMessage) {
                                                const message = document.createElement('div');
                                                message.id = 'preview-limit-message';
                                                message.className = 'alert alert-info mt-3';
                                                message.innerHTML = `<i class="fas fa-info-circle"></i> <strong>Prévisualisation limitée à 30 secondes.</strong> <a href="{{ route('login') }}">Connectez-vous</a> ou <a href="{{ route('cart.add') }}" onclick="event.preventDefault(); document.getElementById('add-to-cart-form').submit();">achetez cette chanson</a> pour l'écouter intégralement.`;
                                                previewPlayer.appendChild(message);
                                                
                                                // Ajouter un bouton pour rejouer la prévisualisation
                                                const replayButton = document.createElement('button');
                                                replayButton.className = 'btn btn-sm btn-outline-primary mt-2';
                                                replayButton.innerHTML = '<i class="fas fa-redo"></i> Rejouer la prévisualisation';
                                                replayButton.onclick = function() {
                                                    previewAudio.currentTime = 0;
                                                    previewAudio.play();
                                                    message.remove();
                                                };
                                                previewPlayer.appendChild(replayButton);
                                            }
                                        }
                                    }
                                    
                                    // Ajouter le gestionnaire d'événement
                                    previewAudio.addEventListener('timeupdate', limitPlaybackDuration);
                                }
                                
                                setTimeout(() => {
                                    previewAudio.play()
                                        .then(() => console.log('Lecture audio démarrée avec succès'))
                                        .catch(e => {
                                            console.error('Erreur lors du démarrage de la lecture:', e);
                                            alert('Le navigateur a bloqué la lecture automatique. Veuillez cliquer sur le lecteur pour démarrer.');
                                        });
                                }, 300);
                            };
                        } else {
                            alert('Prévisualisation non disponible pour cette chanson.');
                            console.error('URL de prévisualisation manquante dans la réponse:', data);
                        }
                        
                        document.body.classList.remove('loading-preview');
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération de la prévisualisation:', error);
                        alert('Impossible de charger la prévisualisation : ' + error.message);
                        if (loadingMessage) {
                            loadingMessage.remove();
                        }
                        document.body.classList.remove('loading-preview');
                        
                        // Réafficher le bouton de prévisualisation en cas d'échec
                        previewPlayer.style.display = 'none';
                        previewButton.style.display = 'block';
                    });
            });
        }
    });
</script>
@endsection

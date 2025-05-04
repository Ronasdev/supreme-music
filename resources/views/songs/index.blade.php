@extends('layouts.app')

@section('title', 'Catalogue de chansons')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="fw-bold mb-0">Catalogue de chansons</h1>
            <p class="text-muted">Découvrez notre collection de musique</p>
        </div>
        <div class="col-md-4 text-end d-flex align-items-center justify-content-end">
            <span class="text-muted me-2">{{ $songs->total() }} titre(s) trouvé(s)</span>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('songs.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Rechercher des chansons..." name="q" value="{{ $query ?? '' }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="genre" class="form-select">
                        <option value="">Tous les genres</option>
                        @foreach($genres as $genreOption)
                            <option value="{{ $genreOption }}" {{ ($genre ?? '') == $genreOption ? 'selected' : '' }}>{{ $genreOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="artist" class="form-select">
                        <option value="">Tous les artistes</option>
                        @foreach($artists as $artistOption)
                            <option value="{{ $artistOption }}" {{ ($artist ?? '') == $artistOption ? 'selected' : '' }}>{{ $artistOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($songs->isEmpty())
        <div class="alert alert-info text-center my-5">
            <i class="fas fa-info-circle me-2"></i>
            Aucune chanson ne correspond à votre recherche. Veuillez essayer avec d'autres critères.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
            @foreach($songs as $song)
                <div class="col">
                    @include('partials.song-card', ['song' => $song])
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $songs->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Section des tendances -->
    @if($songs->count() > 0 && !$query && !$genre && !$artist)
        <div class="mt-5">
            <h2 class="fw-bold mb-4">Tendances du moment</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h3 class="h5 card-title">Les plus écoutés</h3>
                            <ol class="list-group list-group-flush">
                                @foreach($songs->sortByDesc('streams_count')->take(5) as $trendingSong)
                                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ route('songs.show', $trendingSong) }}" class="text-decoration-none">{{ $trendingSong->title }}</a>
                                            <small class="d-block text-muted">{{ $trendingSong->artist }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ $trendingSong->streams_count ?? 0 }} écoutes</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h3 class="h5 card-title">Les plus récents</h3>
                            <ol class="list-group list-group-flush">
                                @foreach($songs->sortByDesc('created_at')->take(5) as $newSong)
                                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center">
                                        <div>
                                            <a href="{{ route('songs.show', $newSong) }}" class="text-decoration-none">{{ $newSong->title }}</a>
                                            <small class="d-block text-muted">{{ $newSong->artist }}</small>
                                        </div>
                                        <span class="badge bg-info rounded-pill">{{ $newSong->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Lecteur de prévisualisation (modal) -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Écouter un extrait</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <h4 id="previewTitle"></h4>
                <p id="previewArtist" class="text-muted"></p>
                <div class="my-4">
                    <audio id="previewAudio" controls class="w-100"></audio>
                </div>
                <div class="mt-3">
                    <p class="text-muted">Ceci est un extrait de 30 secondes. Pour écouter la chanson complète, veuillez l'acheter.</p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="previewBuyLink" class="btn btn-primary">
                    <i class="fas fa-shopping-cart"></i> Acheter
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion de la prévisualisation audio
        const previewButtons = document.querySelectorAll('.btn-preview');
        const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        const previewAudio = document.getElementById('previewAudio');
        const previewTitle = document.getElementById('previewTitle');
        const previewArtist = document.getElementById('previewArtist');
        const previewBuyLink = document.getElementById('previewBuyLink');
        
        previewButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const songId = this.dataset.songId;
                const songTitle = this.dataset.songTitle;
                const songArtist = this.dataset.songArtist;
                const songUrl = this.dataset.songUrl;
                
                // Mettre à jour les informations dans le modal
                previewTitle.textContent = songTitle;
                previewArtist.textContent = songArtist;
                previewBuyLink.href = songUrl;
                
                // Réinitialiser l'audio
                previewAudio.pause();
                previewAudio.src = '';
                
                // Récupérer l'URL de prévisualisation
                fetch(`/songs/${songId}/preview`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.preview_url) {
                            previewAudio.src = data.preview_url;
                            previewAudio.load();
                            previewModal.show();
                            previewAudio.play();
                        } else {
                            alert('Prévisualisation non disponible pour cette chanson.');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération de la prévisualisation:', error);
                        alert('Impossible de charger la prévisualisation.');
                    });
            });
        });
        
        // Arrêter la lecture lorsque le modal est fermé
        document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
            previewAudio.pause();
        });
    });
</script>
@endsection

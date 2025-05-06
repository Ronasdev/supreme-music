@extends('layouts.app')

@section('title', 'Découvrez notre catalogue d\'albums')

@section('content')
<div class="container py-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item active" aria-current="page">Albums</li>
        </ol>
    </nav>

    <h1 class="display-5 fw-bold mb-4">Découvrez notre collection d'albums</h1>
    
    <!-- Filtres de recherche -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('albums.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="q" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Titre ou artiste...">
                </div>
                <div class="col-md-3">
                    <label for="genre" class="form-label">Genre musical</label>
                    <select class="form-select" id="genre" name="genre">
                        <option value="">Tous les genres</option>
                        @foreach($genres as $g)
                            <option value="{{ $g }}" {{ request('genre') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="year" class="form-label">Année</label>
                    <select class="form-select" id="year" name="year">
                        <option value="">Toutes les années</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-2"></i>Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de la recherche -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="mb-0 text-muted">{{ $albums->total() }} album(s) trouvé(s)</p>
        @if(request('q') || request('genre') || request('year'))
            <a href="{{ route('albums.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Effacer les filtres
            </a>
        @endif
    </div>
    
    @if($albums->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @foreach($albums as $album)
                <div class="col">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="position-relative">
                            @if($album->hasCoverImage())
                                <img src="{{ $album->getCoverImageUrl() }}" class="card-img-top" alt="{{ $album->title }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-light text-center py-5" style="height: 180px;">
                                    <i class="fas fa-compact-disc fa-3x text-muted"></i>
                                </div>
                            @endif
                            @if($album->year)
                                <span class="position-absolute top-0 end-0 bg-dark text-white px-2 py-1 m-2 rounded-pill">
                                    {{ $album->year }}
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title fw-bold">{{ $album->title }}</h5>
                            @if($album->artist)
                                <h6 class="card-subtitle mb-2 text-muted">{{ $album->artist }}</h6>
                            @endif
                            @if($album->genre)
                                <div class="mb-2">
                                    <span class="badge bg-secondary">{{ $album->genre }}</span>
                                </div>
                            @endif
                            <p class="card-text small">
                                {{ Str::limit($album->description ?? 'Album de musique envoûtant...', 80) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-info text-dark">
                                    {{ $album->songs_count }} titre{{ $album->songs_count > 1 ? 's' : '' }}
                                </span>
                                @if($album->price > 0)
                                    <span class="fw-bold">{{ number_format($album->price, 2) }} €</span>
                                @else
                                    <span class="badge bg-success">Gratuit</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="{{ route('albums.show', $album) }}" class="btn btn-primary w-100">
                                <i class="fas fa-headphones me-2"></i> Découvrir
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $albums->appends(request()->query())->links() }}
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Aucun album ne correspond à votre recherche. Veuillez essayer avec d'autres critères.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des cartes au survol
        const albumCards = document.querySelectorAll('.hover-shadow');
        albumCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'transform 0.3s ease';
                this.style.boxShadow = '0 .5rem 1rem rgba(0,0,0,.15)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 .125rem .25rem rgba(0,0,0,.075)';
            });
        });
    });
</script>
@endsection
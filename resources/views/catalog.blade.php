@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- En-tête du catalogue -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Catalogue musical</h1>
            <p class="text-muted">Explorez notre sélection de titres et d'albums</p>
        </div>
        <div>
            @auth
                <a href="{{ route('cart.show') }}" class="btn btn-primary rounded-pill shadow-sm">
                    <i class="fas fa-shopping-cart me-2"></i> Mon panier
                    <span class="badge bg-white text-primary ms-2">{{ count(Session::get('cart', [])) }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill">
                    <i class="fas fa-sign-in-alt me-2"></i> Se connecter pour acheter
                </a>
            @endauth
        </div>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="card shadow-sm mb-4 border-0 rounded-lg overflow-hidden">
        <div class="card-body p-4">
            <form action="{{ route('catalog') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-7">
                    <div class="input-group input-group-lg shadow-sm rounded overflow-hidden">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par titre, artiste ou description...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="select-wrapper shadow-sm rounded">
                        <select name="sort" class="form-select form-select-lg border-0">
                            <option value="latest" {{ ($sortBy ?? '') == 'latest' ? 'selected' : '' }}>Plus récents</option>
                            <option value="oldest" {{ ($sortBy ?? '') == 'oldest' ? 'selected' : '' }}>Plus anciens</option>
                            <option value="name" {{ ($sortBy ?? '') == 'name' ? 'selected' : '' }}>Alphabétique</option>
                            <option value="price_asc" {{ ($sortBy ?? '') == 'price_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="price_desc" {{ ($sortBy ?? '') == 'price_desc' ? 'selected' : '' }}>Prix décroissant</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm rounded-pill">
                        <i class="fas fa-filter me-2"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de recherche et filtres actifs -->
    <div class="d-flex flex-wrap align-items-center mb-4">
        <div class="me-auto">
            @if(isset($search) && $search)
                <div class="d-inline-block me-2 mb-2">
                    <span class="badge bg-primary bg-gradient rounded-pill p-2 px-3 d-flex align-items-center">
                        <span class="me-2">Recherche: <strong>{{ $search }}</strong></span>
                        <a href="{{ route('catalog', array_merge(request()->except('search'), ['page' => 1])) }}" class="text-white" title="Supprimer ce filtre">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </span>
                </div>
            @endif
            @if(isset($sortBy) && $sortBy)
                <div class="d-inline-block me-2 mb-2">
                    <span class="badge bg-secondary bg-gradient rounded-pill p-2 px-3 d-flex align-items-center">
                        <span class="me-2">Tri: <strong>
                            @if($sortBy == 'latest') Plus récents
                            @elseif($sortBy == 'oldest') Plus anciens
                            @elseif($sortBy == 'name') Alphabétique
                            @elseif($sortBy == 'price_asc') Prix croissant
                            @elseif($sortBy == 'price_desc') Prix décroissant
                            @endif
                        </strong></span>
                        <a href="{{ route('catalog', array_merge(request()->except('sort'), ['page' => 1])) }}" class="text-white" title="Supprimer ce filtre">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    </span>
                </div>
            @endif
        </div>
        <div>
            <span class="text-muted">{{ $albums->total() }} résultat(s)</span>
        </div>
    </div>

    <!-- Albums -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @forelse($albums as $album)
            <div class="col">
                <div class="card h-100 shadow-hover rounded-lg border-0 overflow-hidden">
                    <div class="position-relative album-cover-container">
                        <a href="{{ route('albums.show', $album) }}" class="cover-link">
                            @if($album->getFirstMedia('cover'))
                                <img src="{{ $album->getFirstMedia('cover')->getUrl() }}" class="card-img-top album-cover" alt="{{ $album->title }}">
                            @else
                                <div class="placeholder-cover d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-music fa-3x text-muted"></i>
                                </div>
                            @endif
                            <div class="cover-overlay">
                                <span class="btn btn-sm btn-light rounded-pill">
                                    <i class="fas fa-eye me-1"></i> Voir les détails
                                </span>
                            </div>
                        </a>
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge bg-primary bg-gradient rounded-pill p-2 shadow-sm">
                                {{ number_format($album->price, 2) }} €
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-1 fw-bold text-truncate">
                            <a href="{{ route('albums.show', $album) }}" class="text-decoration-none text-dark album-title">{{ $album->title }}</a>
                        </h5>
                        <p class="card-text text-muted mb-2">{{ $album->artist }}</p>
                        <div class="d-flex align-items-center">
                            @if($album->songs_count ?? 0)
                                <span class="badge bg-light text-dark rounded-pill me-2">
                                    <i class="fas fa-music me-1"></i> {{ $album->songs_count }} {{ $album->songs_count > 1 ? 'titres' : 'titre' }}
                                </span>
                            @endif
                            <span class="badge bg-light text-dark rounded-pill">
                                <i class="fas fa-calendar me-1"></i> {{ $album->release_date ? date('Y', strtotime($album->release_date)) : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0">
                        <div class="d-flex gap-2">
                            <a href="{{ route('albums.show', $album) }}" class="btn btn-sm btn-outline-primary flex-grow-1 rounded-pill">
                                <i class="fas fa-info-circle me-1"></i> Détails
                            </a>
                            @auth
                                @if(App\Helpers\CartHelper::isInCart($album->id, 'album'))
                                    <a href="{{ route('cart.show') }}" class="btn btn-sm btn-success flex-grow-1 rounded-pill">
                                        <i class="fas fa-check me-1"></i> Dans le panier
                                    </a>
                                @else
                                    <form action="{{ route('cart.add') }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $album->id }}">
                                        <input type="hidden" name="type" value="album">
                                        <button type="submit" class="btn btn-sm btn-outline-primary w-100 rounded-pill">
                                            <i class="fas fa-shopping-cart me-1"></i> Ajouter
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary flex-grow-1 rounded-pill">
                                    <i class="fas fa-sign-in-alt me-1"></i> Se connecter
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info rounded-lg shadow-sm">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
                        <div>
                            <h5 class="mb-1">Aucun résultat trouvé</h5>
                            <p class="mb-0">Essayez de modifier vos critères de recherche ou explorez notre catalogue complet.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $albums->withQueryString()->links() }}
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Styles améliorés pour le catalogue */
    .shadow-hover {
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        position: relative;
        top: 0;
    }
    
    .shadow-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    
    .album-cover-container {
        height: 200px;
        overflow: hidden;
    }
    
    .album-cover {
        height: 100%;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .placeholder-cover {
        height: 200px;
        width: 100%;
        background-color: #f8f9fa;
    }
    
    .cover-link {
        display: block;
        position: relative;
    }
    
    .cover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .cover-link:hover .album-cover {
        transform: scale(1.05);
    }
    
    .cover-link:hover .cover-overlay {
        opacity: 1;
    }
    
    .album-title {
        color: var(--primary-color);
        transition: color 0.2s ease;
    }
    
    .album-title:hover {
        color: var(--primary-dark);
    }
    
    .select-wrapper {
        position: relative;
    }
    
    .select-wrapper::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
        pointer-events: none;
    }
    
    /* Animation des badges */
    .badge.bg-primary,
    .badge.bg-secondary {
        transition: all 0.2s ease;
    }
    
    .badge.bg-primary:hover,
    .badge.bg-secondary:hover {
        transform: translateY(-2px);
    }
</style>
@endsection

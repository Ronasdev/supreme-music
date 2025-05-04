@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Catalogue musical</h1>
        <div>
            @auth
                <a href="{{ route('cart.show') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-cart me-2"></i> Mon panier
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-2"></i> Se connecter pour acheter
                </a>
            @endauth
        </div>
    </div>

    <!-- Barre de recherche et filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('catalog') }}" method="GET" class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par titre, artiste ou description...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="latest" {{ ($sortBy ?? '') == 'latest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="oldest" {{ ($sortBy ?? '') == 'oldest' ? 'selected' : '' }}>Plus anciens</option>
                        <option value="name" {{ ($sortBy ?? '') == 'name' ? 'selected' : '' }}>Alphabétique</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats de recherche -->
    @if(isset($search) && $search)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> Résultats pour la recherche: <strong>{{ $search }}</strong>
        </div>
    @endif

    <!-- Albums -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @forelse($albums as $album)
            <div class="col">
                <div class="card h-100 shadow-sm hover-shadow">
                    <div class="position-relative">
                        @if($album->getFirstMedia('cover'))
                            <img src="{{ $album->getFirstMedia('cover')->getUrl() }}" class="card-img-top" alt="{{ $album->title }}">
                        @else
                            <img src="https://placehold.co/300x300?text=Album" class="card-img-top" alt="{{ $album->title }}">
                        @endif
                        <div class="position-absolute top-0 end-0 p-2">
                            <span class="badge bg-primary rounded-pill">{{ number_format($album->price, 2) }} €</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $album->title }}</h5>
                        <p class="card-text text-muted">{{ $album->artist }}</p>
                        <p class="card-text small">
                            @if($album->songs_count ?? 0)
                                <span class="badge bg-light text-dark">{{ $album->songs_count }} chansons</span>
                            @endif
                        </p>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <a href="{{ route('albums.show', $album) }}" class="btn btn-sm btn-outline-info">Détails</a>
                        @auth
                            <form action="{{ route('cart.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $album->id }}">
                                <input type="hidden" name="type" value="album">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Ajouter au panier</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Se connecter</a>
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Aucun album trouvé.
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
    .hover-shadow:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease-in-out;
        box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection

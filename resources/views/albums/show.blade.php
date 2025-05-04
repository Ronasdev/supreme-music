@extends('layouts.app')

@section('title', $album->title)

@section('content')
<div class="container my-4">
    <!-- Fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('albums.index') }}">Albums</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $album->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Image de l'album -->
        <div class="col-lg-5 mb-4 mb-lg-0">
            <div class="position-relative">
                @if($album->getFirstMedia('cover'))
                    <img src="{{ $album->getFirstMediaUrl('cover') }}" alt="{{ $album->title }}" class="img-fluid rounded shadow-lg album-cover-animation">
                @else
                    <img src="https://placehold.co/600x600?text=Album" alt="{{ $album->title }}" class="img-fluid rounded shadow-lg album-cover-animation">
                @endif
                <div class="position-absolute top-0 end-0 p-3">
                    <span class="badge bg-primary rounded-pill fs-5">{{ number_format($album->price, 2) }} €</span>
                </div>
                @if($album->year)
                    <div class="position-absolute bottom-0 start-0 p-3">
                        <span class="badge bg-dark rounded-pill">{{ $album->year }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Informations sur l'album -->
        <div class="col-lg-7">
            <h1 class="fw-bold mb-3">{{ $album->title }}</h1>
            
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if($album->artist)
                    <span class="badge bg-secondary text-white"><i class="fas fa-user me-1"></i> {{ $album->artist }}</span>
                @endif
                @if($album->genre)
                    <span class="badge bg-info text-dark"><i class="fas fa-music me-1"></i> {{ $album->genre }}</span>
                @endif
                @if($album->year)
                    <span class="badge bg-dark text-white"><i class="fas fa-calendar me-1"></i> {{ $album->year }}</span>
                @endif
                <span class="badge bg-light text-dark"><i class="fas fa-compact-disc me-1"></i> {{ count($album->songs) }} titre(s)</span>
                <span class="badge bg-light text-dark"><i class="fas fa-eye me-1"></i> {{ number_format($album->views_count ?? 0) }} vues</span>
            </div>
            
            <div class="mb-4">
                <h5 class="fw-bold">Description</h5>
                <p class="lead">{{ $album->description ?? 'Aucune description disponible.' }}</p>
            </div>
            
            <!-- Actions -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                @auth
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $album->id }}">
                        <input type="hidden" name="type" value="album">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-shopping-cart me-2"></i> Ajouter au panier
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt me-2"></i> Connectez-vous pour acheter
                    </a>
                @endauth
                
                <a href="{{ route('albums.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Retour aux albums
                </a>
            </div>
        </div>
    </div>

    <!-- Liste des chansons -->
    <div class="card shadow-sm mt-5">
        <div class="card-header bg-light">
            <h2 class="h4 mb-0">Liste des chansons</h2>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Titre</th>
                            <th style="width: 120px">Durée</th>
                            <th style="width: 150px">Prix</th>
                            <th style="width: 200px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($album->songs as $index => $song)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $song->title }}</td>
                                <td>{{ $song->duration ?? '--:--' }}</td>
                                <td>{{ number_format($song->price, 2) }} €</td>
                                <td>
                                    <a href="{{ route('songs.show', $song) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    
                                    @auth
                                        @if(Auth::user()->orders()->whereHas('orderItems', function($query) use ($album) {
                                            $query->where('item_id', $album->id)->where('item_type', 'album');
                                        })->where('status', 'paid')->exists())
                                            <a href="{{ route('stream.play', $song) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-play"></i> Écouter
                                            </a>
                                        @elseif(Auth::user()->orders()->whereHas('orderItems', function($query) use ($song) {
                                            $query->where('item_id', $song->id)->where('item_type', 'song');
                                        })->where('status', 'paid')->exists())
                                            <a href="{{ route('stream.play', $song) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-play"></i> Écouter
                                            </a>
                                        @else
                                            <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $song->id }}">
                                                <input type="hidden" name="type" value="song">
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-lock"></i>
                                        </a>
                                    @endauth
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-3">Aucune chanson disponible dans cet album.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Recommandations -->
    <div class="mt-5">
        <h3 class="fw-bold mb-4">Vous pourriez aussi aimer</h3>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($similarAlbums ?? App\Models\Album::where('id', '!=', $album->id)
                ->when($album->genre, function($query, $genre) {
                    $query->where('genre', $genre);
                })
                ->when($album->artist, function($query, $artist) {
                    $query->orWhere('artist', $artist);
                })
                ->inRandomOrder()->limit(4)->get() as $relatedAlbum)
                <div class="col">
                    <div class="card h-100 shadow-sm hover-shadow">
                        <div class="position-relative">
                            @if($relatedAlbum->getFirstMedia('cover'))
                                <img src="{{ $relatedAlbum->getFirstMediaUrl('cover') }}" class="card-img-top album-cover-animation" alt="{{ $relatedAlbum->title }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-light text-center py-5 album-cover-animation" style="height: 180px;">
                                    <i class="fas fa-compact-disc fa-3x text-muted"></i>
                                </div>
                            @endif
                            @if($relatedAlbum->year)
                                <span class="position-absolute top-0 end-0 bg-dark text-white px-2 py-1 m-2 rounded-pill">
                                    {{ $relatedAlbum->year }}
                                </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $relatedAlbum->title }}</h5>
                            @if($relatedAlbum->artist)
                                <p class="card-text text-muted"><i class="fas fa-user me-1"></i> {{ $relatedAlbum->artist }}</p>
                            @endif
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @if($relatedAlbum->genre)
                                    <span class="badge bg-secondary">{{ $relatedAlbum->genre }}</span>
                                @endif
                                @if($relatedAlbum->year)
                                    <span class="badge bg-dark">{{ $relatedAlbum->year }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="{{ route('albums.show', $relatedAlbum) }}" class="btn btn-sm btn-outline-primary w-100">Voir l'album</a>
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
    
    .badge {
        transition: all 0.2s ease;
    }
    
    .badge:hover {
        transform: scale(1.05);
    }
    
    /* Animation des images */
    .album-cover-animation {
        transition: all 0.5s ease;
    }
    
    .album-cover-animation:hover {
        transform: scale(1.02) rotate(1deg);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des cartes au survol
        const albumCovers = document.querySelectorAll('.album-cover-animation');
        albumCovers.forEach(cover => {
            cover.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.02) rotate(1deg)';
                this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
            });
            cover.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1) rotate(0deg)';
                this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
            });
        });
    });
</script>
@endsection
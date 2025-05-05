@extends('layouts.app')

@section('content')
<!-- Section Hero -->
<div class="container my-5">
  <div class="row align-items-center">
    <div class="col-md-6 text-center text-md-start">
      <h1 class="display-4 fw-bold mb-4">Bienvenue sur Supreme Musique 🎧</h1>
      <p class="lead mb-4">Découvrez, achetez et écoutez de la musique en streaming. Notre catalogue s'agrandit tous les jours avec les meilleurs titres.</p>
      <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
        <a href="{{ route('catalog') }}" class="btn btn-primary btn-lg">Explorer le catalogue</a>
        <a href="{{ route('how-it-works') }}" class="btn btn-outline-secondary btn-lg">Comment ça marche</a>
      </div>
    </div>
    <div class="col-md-6 d-none d-md-block">
      <img src="{{ asset('images/music-hero.jpg') }}" alt="Supreme Musique" class="img-fluid rounded shadow-lg" onerror="this.src='https://placehold.co/600x400?text=Supreme+Musique'">
    </div>
  </div>
</div>

<!-- Section Albums en vedette -->
<div class="container my-5">
  <h2 class="fw-bold mb-4">Albums en vedette <i class="fas fa-star text-warning"></i></h2>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    @forelse($featuredAlbums as $album)
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
            <a href="{{ route('albums.show', $album) }}" class="btn btn-sm btn-outline-primary">Voir l'album</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-info">Aucun album en vedette pour le moment.</div>
      </div>
    @endforelse
  </div>
  <div class="text-end mt-3">
    <a href="{{ route('albums.index') }}" class="btn btn-link">Voir tous les albums <i class="fas fa-arrow-right"></i></a>
  </div>
</div>

<!-- Section Dernières chansons -->
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Dernières chansons</h2>
      <p class="text-muted">Les titres les plus récents ajoutés à notre catalogue</p>
    </div>
    <a href="{{ route('catalog') }}" class="btn btn-outline-primary rounded-pill">
      <i class="fas fa-music me-2"></i> Voir toutes les chansons
    </a>
  </div>
  
  <div class="card shadow-sm border-0 rounded-lg overflow-hidden mb-4">
    <div class="card-body p-0">
      @forelse($latestSongs as $index => $song)
        <div class="song-row d-flex align-items-center p-3 {{ $index % 2 == 0 ? 'bg-white' : 'bg-light' }} border-bottom hover-bg">
          <!-- Numéro et miniature -->
          <div class="d-flex align-items-center" style="width: 15%">
            <div class="me-3 text-muted fw-bold">{{ $index + 1 }}</div>
            <div class="song-cover-wrapper">
              @if($song->album && $song->album->getFirstMedia('cover'))
                <img src="{{ $song->album->getFirstMediaUrl('cover', 'thumbnail') }}" 
                     alt="{{ $song->title }}" 
                     class="rounded song-cover shadow-sm">
              @else
                <div class="bg-light rounded song-cover shadow-sm d-flex align-items-center justify-content-center">
                  <i class="fas fa-music text-muted"></i>
                </div>
              @endif
            </div>
          </div>
          
          <!-- Informations chanson -->
          <div class="d-flex flex-column" style="width: 40%">
            <h6 class="mb-0 text-truncate fw-bold">{{ $song->title }}</h6>
            <span class="text-muted small">{{ $song->artist }}</span>
          </div>
          
          <!-- Nom de l'album -->
          <div style="width: 20%">
            @if($song->album)
              <a href="{{ route('albums.show', $song->album) }}" class="album-link text-decoration-none text-truncate">
                {{ $song->album->title }}
              </a>
            @else
              <span class="text-muted">Single</span>
            @endif
          </div>
          
          <!-- Prix -->
          <div style="width: 10%">
            <span class="badge bg-primary bg-gradient rounded-pill p-2 shadow-sm">
              {{ number_format($song->price, 2) }} €
            </span>
          </div>
          
          <!-- Actions -->
          <div class="text-end" style="width: 15%">
            <div class="btn-group">
              <a href="{{ route('songs.show', $song) }}" class="btn btn-sm btn-outline-primary rounded-pill-left">
                <i class="fas fa-info-circle"></i>
              </a>
              @auth
                @if($isInCart($song->id, 'song'))
                  <a href="{{ route('cart.show') }}" class="btn btn-sm btn-success rounded-pill-right">
                    <i class="fas fa-shopping-cart"></i>
                  </a>
                @else
                  <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="id" value="{{ $song->id }}">
                    <input type="hidden" name="type" value="song">
                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill-right">
                      <i class="fas fa-cart-plus"></i>
                    </button>
                  </form>
                @endif
              @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary rounded-pill-right">
                  <i class="fas fa-sign-in-alt"></i>
                </a>
              @endauth
            </div>
          </div>
        </div>
      @empty
        <div class="text-center py-5">
          <div class="mb-3">
            <i class="fas fa-music fa-3x text-muted"></i>
          </div>
          <h5>Aucune chanson disponible pour le moment</h5>
          <p class="text-muted">Notre catalogue sera bientôt enrichi de nouveaux titres.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>

<!-- Section pour utilisateurs connectés -->
@auth
  <div class="container my-5">
    <h2 class="fw-bold mb-4">Vos playlists <i class="fas fa-headphones text-success"></i></h2>
    
    @if(count($userPlaylists) > 0)
      <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($userPlaylists as $playlist)
          <div class="col">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title">{{ $playlist->name }}</h5>
                <p class="card-text">{{ $playlist->songs_count }} chansons</p>
                <a href="{{ route('playlists.show', $playlist) }}" class="btn btn-sm btn-outline-success">Écouter</a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
      <div class="text-end mt-3">
        <a href="{{ route('playlists.index') }}" class="btn btn-link">Voir toutes vos playlists <i class="fas fa-arrow-right"></i></a>
      </div>
    @else
      <div class="alert alert-info">
        <p>Vous n'avez pas encore créé de playlist.</p>
        <a href="{{ route('playlists.create') }}" class="btn btn-outline-primary">Créer ma première playlist</a>
      </div>
    @endif
  </div>
@endauth
@endsection

@section('styles')
<style>
  /* Animation des cartes d'albums */
  .hover-shadow:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease-in-out;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
  }
  
  /* Styles pour la liste des dernières chansons */
  .song-cover-wrapper {
    position: relative;
    overflow: hidden;
  }
  
  .song-cover {
    width: 45px;
    height: 45px;
    object-fit: cover;
    transition: transform 0.3s ease;
  }
  
  .hover-bg {
    transition: background-color 0.2s ease;
  }
  
  .hover-bg:hover {
    background-color: #f8f9fa !important;
  }
  
  .song-row {
    transition: all 0.2s ease;
  }
  
  .song-row:hover .song-cover {
    transform: scale(1.1);
  }
  
  /* Boutons avec coins arrondis spéciaux */
  .rounded-pill-left {
    border-top-left-radius: 50rem !important;
    border-bottom-left-radius: 50rem !important;
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
  }
  
  .rounded-pill-right {
    border-top-right-radius: 50rem !important;
    border-bottom-right-radius: 50rem !important;
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
  }
  
  /* Liens d'album */
  .album-link {
    color: var(--primary-color);
    display: inline-block;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    transition: color 0.2s ease;
  }
  
  .album-link:hover {
    color: var(--primary-dark);
    text-decoration: underline !important;
  }
</style>
@endsection
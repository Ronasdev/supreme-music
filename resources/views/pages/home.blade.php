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
  <h2 class="fw-bold mb-4">Dernières chansons <i class="fas fa-music text-info"></i></h2>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th style="width: 50px">#</th>
          <th>Titre</th>
          <th>Album</th>
          <th>Prix</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($latestSongs as $index => $song)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>
              <div class="d-flex align-items-center">
                <div class="me-3">
                  @if($song->album && $song->album->getFirstMedia('cover'))
                    <img src="{{ $song->album->getFirstMedia('cover')->getUrl() }}" alt="{{ $song->title }}" style="width: 40px; height: 40px;" class="rounded shadow-sm">
                  @else
                    <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                      <i class="fas fa-music text-muted"></i>
                    </div>
                  @endif
                </div>
                <div>
                  {{ $song->title }}
                </div>
              </div>
            </td>
            <td>{{ $song->album ? $song->album->title : 'Single' }}</td>
            <td>{{ number_format($song->price, 2) }} €</td>
            <td>
              <a href="{{ route('songs.show', $song) }}" class="btn btn-sm btn-outline-info">Détails</a>
              @auth
                <form action="{{ route('cart.add') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="id" value="{{ $song->id }}">
                  <input type="hidden" name="type" value="song">
                  <button type="submit" class="btn btn-sm btn-outline-primary">Ajouter au panier</button>
                </form>
              @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Connectez-vous pour acheter</a>
              @endauth
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center py-3">Aucune chanson disponible pour le moment.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="text-end mt-3">
    <a href="{{ route('catalog') }}" class="btn btn-link">Explorer tout le catalogue <i class="fas fa-arrow-right"></i></a>
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
  .hover-shadow:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease-in-out;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1) !important;
  }
</style>
@endsection
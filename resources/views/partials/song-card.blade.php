<div class="card h-100 shadow-sm">
    @if($song->album && $song->album->hasCoverImage())
        <img src="{{ $song->album->getCoverImageUrl() }}" class="card-img-top" alt="{{ $song->title }}" style="height: 180px; object-fit: cover;">
    @else
        <div class="bg-light text-center py-5" style="height: 180px;">
            <i class="fas fa-music fa-3x text-muted"></i>
        </div>
    @endif
    <div class="card-body">
        <h5 class="card-title">{{ $song->title }}</h5>
        <p class="card-text text-muted">
            {{ $song->artist ?? ($song->album ? $song->album->artist : 'Artiste inconnu') }}
            @if($song->album)
                <br><small>Album: {{ $song->album->title }}</small>
            @endif
        </p>
        <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-primary">{{ number_format($song->price, 2) }} €</span>
            <small class="text-muted">{{ $song->duration ?? '3:45' }}</small>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('songs.show', $song) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye me-1"></i> Détails
        </a>
        @auth
            @if(Auth::user()->hasPurchased($song))
                <a href="{{ route('stream.play', $song) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-play me-1"></i> Écouter
                </a>
            @else
                @if($isInCart($song->id, 'song'))
                    <a href="{{ route('cart.show') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i> Dans le panier
                    </a>
                @else
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $song->id }}">
                        <input type="hidden" name="type" value="song">
                        <button type="submit" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-shopping-cart me-1"></i> Ajouter
                        </button>
                    </form>
                @endif
            @endif
        @else
            @if($isInCart($song->id, 'song'))
                <a href="{{ route('cart.show') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-check me-1"></i> Dans le panier
                </a>
            @else
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $song->id }}">
                    <input type="hidden" name="type" value="song">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-shopping-cart me-1"></i> Ajouter
                    </button>
                </form>
            @endif
        @endauth
    </div>
</div>

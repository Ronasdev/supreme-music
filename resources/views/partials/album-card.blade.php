<div class="card h-100 shadow-sm">
    @if($album->hasCoverImage())
        <img src="{{ $album->getCoverImageUrl() }}" class="card-img-top" alt="{{ $album->title }}" style="height: 180px; object-fit: cover;">
    @else
        <div class="bg-light text-center py-5" style="height: 180px;">
            <i class="fas fa-compact-disc fa-3x text-muted"></i>
        </div>
    @endif
    <div class="card-body">
        <h5 class="card-title">{{ $album->title }}</h5>
        <p class="card-text text-muted">{{ $album->artist }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-primary">{{ number_format($album->price, 2) }} €</span>
            <small class="text-muted">{{ $album->songs->count() }} chansons</small>
        </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('albums.show', $album) }}" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye me-1"></i> Détails
        </a>
        @auth
            @if(Auth::user()->hasPurchased($album))
                <span class="badge bg-success">Acheté</span>
            @else
                @if($isInCart($album->id, 'album'))
                    <a href="{{ route('cart.show') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-check me-1"></i> Dans le panier
                    </a>
                @else
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $album->id }}">
                        <input type="hidden" name="type" value="album">
                        <button type="submit" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-shopping-cart me-1"></i> Ajouter
                        </button>
                    </form>
                @endif
            @endif
        @else
            @if($isInCart($album->id, 'album'))
                <a href="{{ route('cart.show') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-check me-1"></i> Dans le panier
                </a>
            @else
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $album->id }}">
                    <input type="hidden" name="type" value="album">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-shopping-cart me-1"></i> Ajouter
                    </button>
                </form>
            @endif
        @endauth
    </div>
</div>

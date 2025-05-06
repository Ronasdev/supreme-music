@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Mon panier</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(count($items ?? []) > 0)
        <div class="row">
            <!-- Tableau des articles -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Article</th>
                                        <th style="width: 150px" class="text-end">Prix</th>
                                        <th style="width: 100px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        @if($item['type'] == 'album' && $item['product']->hasCoverImage())
                                                            <img src="{{ $item['product']->getCoverImageUrl() }}" 
                                                                 alt="{{ $item['product']->title }}" 
                                                                 style="width: 50px; height: 50px;" 
                                                                 class="rounded shadow-sm">
                                                        @elseif($item['type'] == 'song' && $item['product']->album && $item['product']->album->hasCoverImage())
                                                            <img src="{{ $item['product']->album->getCoverImageUrl() }}" 
                                                                 alt="{{ $item['product']->title }}" 
                                                                 style="width: 50px; height: 50px;" 
                                                                 class="rounded shadow-sm">
                                                        @else
                                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                                 style="width: 50px; height: 50px;">
                                                                <i class="fas {{ $item['type'] == 'album' ? 'fa-compact-disc' : 'fa-music' }} text-muted"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $item['product']->title }}</h6>
                                                        <div class="small text-muted">
                                                            @if($item['type'] == 'album')
                                                                Album • {{ $item['product']->artist }}
                                                            @else
                                                                Chanson
                                                                @if($item['product']->album)
                                                                    • Album: {{ $item['product']->album->title }}
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">{{ number_format($item['price'], 2) }} €</td>
                                            <td class="text-end pe-3">
                                                <form action="{{ route('cart.remove', $item['cart_key'] ?? $item['id'] . '_' . $item['type']) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer du panier">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                                {{-- Commentaire : Nous utilisons prioritairement cart_key si disponible, sinon nous reconstruisons l'ID --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('catalog') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Continuer mes achats
                    </a>
                    
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-2"></i> Vider le panier
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Résumé de la commande -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h3 class="card-title h5 mb-0">Résumé de la commande</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total</span>
                            <span>{{ number_format($total, 2) }} €</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                            <span>TVA (incluse)</span>
                            <span>{{ number_format($total * 0.2, 2) }} €</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>Total</span>
                            <span>{{ number_format($total, 2) }} €</span>
                        </div>
                        
                        <form action="{{ route('orders.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check me-2"></i> Procéder au paiement
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Mode de paiement</h5>
                        <p class="card-text small">
                            <i class="fas fa-info-circle me-2 text-info"></i>
                            Nous acceptons le paiement via Orange Money. Vous serez redirigé vers la page de paiement après avoir confirmé votre commande.
                        </p>
                        <div class="d-flex justify-content-center mt-3">
                            <img src="{{ asset('images/orange-money-logo.png') }}" alt="Orange Money" class="img-fluid" style="max-height: 50px;" onerror="this.src='https://placehold.co/200x50?text=Orange+Money'">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-lg border-0 rounded-3 overflow-hidden">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-5x text-muted"></i>
                </div>
                <h2 class="mb-3">Votre panier est vide</h2>
                <p class="lead mb-4">Explorez notre catalogue et ajoutez des articles à votre panier.</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary">
                    <i class="fas fa-music me-2"></i> Parcourir le catalogue
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Fil d'Ariane -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Mes commandes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orders.show', $order) }}">Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Paiement</li>
                </ol>
            </nav>

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

            <!-- Récapitulatif de commande -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h2 class="h4 mb-0">Récapitulatif de la commande</h2>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Numéro de commande</p>
                            <p class="fw-bold">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Date de commande</p>
                            <p>{{ $order->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Article</th>
                                    <th class="text-end">Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            @if($item->item_type == 'album')
                                                @php
                                                    $album = \App\Models\Album::find($item->item_id);
                                                @endphp
                                                @if($album)
                                                    {{ $album->title }} (Album)
                                                @else
                                                    Album #{{ $item->item_id }}
                                                @endif
                                            @elseif($item->item_type == 'song')
                                                @php
                                                    $song = \App\Models\Song::find($item->item_id);
                                                @endphp
                                                @if($song)
                                                    {{ $song->title }} (Chanson)
                                                @else
                                                    Chanson #{{ $item->item_id }}
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format($item->price, 2) }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-bold">Total</td>
                                    <td class="text-end fw-bold">{{ number_format($order->total_price, 2) }} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Formulaire de paiement -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="h4 mb-0">Paiement via Orange Money</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/orange-money-logo.png') }}" alt="Orange Money" class="img-fluid" style="max-height: 80px;" onerror="this.src='https://placehold.co/200x80?text=Orange+Money'">
                    </div>

                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="h5 alert-heading">Comment ça marche ?</h4>
                                <p class="mb-0">Pour effectuer votre paiement via Orange Money :</p>
                                <ol class="mb-0 mt-2">
                                    <li>Entrez votre numéro de téléphone Orange Money ci-dessous</li>
                                    <li>Vous recevrez un code de confirmation sur votre téléphone</li>
                                    <li>Validez le paiement en saisissant votre code secret Orange Money</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('payments.initiate', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Numéro de téléphone Orange Money</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" placeholder="Ex: 07XXXXXXXX" value="{{ old('phone_number') }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Entrez votre numéro Orange Money sans indicatif international.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-money-bill-wave me-2"></i> Procéder au paiement
                            </button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Retour à la commande
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sécurité des paiements -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="h5 mb-3"><i class="fas fa-shield-alt me-2 text-success"></i> Paiement sécurisé</h4>
                    <p class="small mb-0">
                        Toutes vos transactions sont sécurisées. Nous ne stockons pas vos informations de paiement.
                        Vos données personnelles sont protégées conformément à notre politique de confidentialité.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

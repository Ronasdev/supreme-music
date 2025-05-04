@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="fw-bold mb-4">Mes commandes</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(count($orders) > 0)
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">N° Commande</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-bold">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->total_price, 2) }} €</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning text-dark">En attente</span>
                                        @elseif($order->status == 'paid')
                                            <span class="badge bg-success">Payée</span>
                                        @elseif($order->status == 'failed')
                                            <span class="badge bg-danger">Échouée</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Détails
                                        </a>
                                        
                                        @if($order->status == 'pending')
                                            <a href="{{ route('payments.show', $order) }}" class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-credit-card me-1"></i> Payer
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-shopping-bag fa-5x text-muted"></i>
                </div>
                <h2 class="mb-3">Vous n'avez pas encore passé de commande</h2>
                <p class="lead mb-4">Explorez notre catalogue et commencez vos achats dès maintenant !</p>
                <a href="{{ route('catalog') }}" class="btn btn-primary">
                    <i class="fas fa-music me-2"></i> Parcourir le catalogue
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

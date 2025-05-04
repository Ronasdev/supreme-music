@extends('layouts.admin')

@section('title', 'Gestion des paiements')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des paiements</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
        <li class="breadcrumb-item active">Paiements</li>
    </ol>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    Nombre total de paiements
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    Paiements réussis
                    <h4 class="mb-0">{{ $stats['successful'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    Paiements échoués
                    <h4 class="mb-0">{{ $stats['failed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    Revenu total
                    <h4 class="mb-0">{{ number_format($stats['total_revenue'], 2, ',', ' ') }} €</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filtrer les paiements
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Statut</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="paid" {{ $status == 'paid' ? 'selected' : '' }}>Payé</option>
                        <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>Échoué</option>
                        <option value="refunded" {{ $status == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user" class="form-label">Client</label>
                    <select name="user" id="user" class="form-select">
                        <option value="">Tous les clients</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Date début</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filtrer</button>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Liste des paiements
            </div>
            <div>
                <a href="{{ route('admin.payments.report', request()->query()) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-csv"></i> Exporter CSV
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Transaction</th>
                        <th>Méthode</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $payment->user->name }}</td>
                            <td>{{ $payment->transaction_id }}</td>
                            <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                            <td>{{ number_format($payment->total_price, 2, ',', ' ') }} €</td>
                            <td>
                                @if($payment->status == 'paid')
                                    <span class="badge bg-success">Payé</span>
                                @elseif($payment->status == 'pending')
                                    <span class="badge bg-warning">En attente</span>
                                @elseif($payment->status == 'failed')
                                    <span class="badge bg-danger">Échoué</span>
                                @elseif($payment->status == 'refunded')
                                    <span class="badge bg-info">Remboursé</span>
                                @else
                                    <span class="badge bg-secondary">{{ $payment->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.payments.show', $payment) }}" class="btn btn-sm btn-primary" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucun paiement trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-4">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

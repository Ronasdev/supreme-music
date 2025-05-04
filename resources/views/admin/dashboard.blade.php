@extends('layouts.admin')

@section('styles')
<style>
  .stat-card {
    border-radius: 10px;
    border: none;
    transition: transform 0.3s;
  }
  .stat-card:hover {
    transform: translateY(-5px);
  }
  .stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
  }
  .action-card {
    border-radius: 10px;
    transition: all 0.3s;
    height: 100%;
  }
  .action-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
  }
  .recent-item:hover {
    background-color: rgba(0,0,0,0.03);
  }
</style>
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Tableau de bord administrateur</h2>
    <div>
      <a href="{{ route('home') }}" class="btn btn-outline-primary btn-sm me-2" target="_blank">
        <i class="fas fa-external-link-alt me-1"></i> Voir le site
      </a>
      <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-cog me-1"></i> Paramètres
      </a>
    </div>
  </div>

  <!-- Résumé statistique -->
  <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card bg-primary text-white shadow-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h3 class="mb-0 fw-bold">{{ $totalAlbums }}</h3>
              <p class="mb-0 opacity-75">Albums</p>
            </div>
            <div class="stat-icon">
              <i class="fas fa-compact-disc"></i>
            </div>
          </div>
          <div class="progress bg-white bg-opacity-25 mt-3" style="height: 5px;">
            <div class="progress-bar bg-white" style="width: 100%"></div>
          </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-2">
          <a href="{{ route('admin.albums.index') }}" class="text-white d-flex align-items-center">
            <small>Voir tous les albums</small>
            <i class="fas fa-arrow-right ms-auto"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card bg-success text-white shadow-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h3 class="mb-0 fw-bold">{{ $totalSongs ?? 0 }}</h3>
              <p class="mb-0 opacity-75">Titres</p>
            </div>
            <div class="stat-icon">
              <i class="fas fa-music"></i>
            </div>
          </div>
          <div class="progress bg-white bg-opacity-25 mt-3" style="height: 5px;">
            <div class="progress-bar bg-white" style="width: 100%"></div>
          </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-2">
          <a href="{{ route('admin.songs.index') }}" class="text-white d-flex align-items-center">
            <small>Voir tous les titres</small>
            <i class="fas fa-arrow-right ms-auto"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card bg-danger text-white shadow-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h3 class="mb-0 fw-bold">{{ $totalOrders }}</h3>
              <p class="mb-0 opacity-75">Commandes</p>
            </div>
            <div class="stat-icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
          <div class="progress bg-white bg-opacity-25 mt-3" style="height: 5px;">
            <div class="progress-bar bg-white" style="width: 100%"></div>
          </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-2">
          <a href="{{ route('admin.orders.index') }}" class="text-white d-flex align-items-center">
            <small>Voir toutes les commandes</small>
            <i class="fas fa-arrow-right ms-auto"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
      <div class="card stat-card bg-info text-white shadow-3">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h3 class="mb-0 fw-bold">{{ $totalUsers ?? 0 }}</h3>
              <p class="mb-0 opacity-75">Utilisateurs</p>
            </div>
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
          </div>
          <div class="progress bg-white bg-opacity-25 mt-3" style="height: 5px;">
            <div class="progress-bar bg-white" style="width: 100%"></div>
          </div>
        </div>
        <div class="card-footer bg-transparent border-0 py-2">
          <a href="{{ route('admin.users.index') }}" class="text-white d-flex align-items-center">
            <small>Voir tous les utilisateurs</small>
            <i class="fas fa-arrow-right ms-auto"></i>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Actions rapides -->
  <h4 class="mb-3"><i class="fas fa-bolt me-2"></i> Actions rapides</h4>
  <div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card action-card shadow-2">
        <div class="card-body text-center">
          <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-flex mb-3">
            <i class="fas fa-plus fa-2x text-primary"></i>
          </div>
          <h5>Nouvel album</h5>
          <p class="text-muted small">Ajouter un nouvel album au catalogue</p>
          <a href="{{ route('admin.albums.create') }}" class="btn btn-primary btn-sm w-100">Créer</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card action-card shadow-2">
        <div class="card-body text-center">
          <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-flex mb-3">
            <i class="fas fa-music fa-2x text-success"></i>
          </div>
          <h5>Nouveau titre</h5>
          <p class="text-muted small">Ajouter un nouveau titre à un album</p>
          <a href="{{ route('admin.songs.create') }}" class="btn btn-success btn-sm w-100">Créer</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card action-card shadow-2">
        <div class="card-body text-center">
          <div class="rounded-circle bg-danger bg-opacity-10 p-3 d-inline-flex mb-3">
            <i class="fas fa-receipt fa-2x text-danger"></i>
          </div>
          <h5>Commandes</h5>
          <p class="text-muted small">Gérer les commandes clients</p>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-danger btn-sm w-100">Gérer</a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
      <div class="card action-card shadow-2">
        <div class="card-body text-center">
          <div class="rounded-circle bg-info bg-opacity-10 p-3 d-inline-flex mb-3">
            <i class="fas fa-chart-bar fa-2x text-info"></i>
          </div>
          <h5>Statistiques</h5>
          <p class="text-muted small">Voir les rapports détaillés</p>
          <a href="{{ route('admin.stats') }}" class="btn btn-info btn-sm w-100 text-white">Consulter</a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Dernières commandes -->
    <div class="col-lg-7 mb-4">
      <div class="card shadow-2">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Dernières commandes</h5>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-link">Voir tout</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>#ID</th>
                  <th>Client</th>
                  <th>Album</th>
                  <th>Montant</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($latestOrders as $order)
                  <tr class="recent-item">
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->user->name ?? 'Client inconnu' }}</td>
                    <td>{{ $order->album->title ?? 'Inconnu' }}</td>
                    <td>{{ number_format($order->amount ?? 0, 2) }} €</td>
                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                      <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center py-3">Aucune commande récente</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Activité récente -->
    <div class="col-lg-5 mb-4">
      <div class="card shadow-2 h-100">
        <div class="card-header bg-light">
          <h5 class="mb-0">Activité récente</h5>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex align-items-center recent-item p-3">
              <div class="avatar-wrapper me-3 bg-primary bg-opacity-10 rounded-circle p-2">
                <i class="fas fa-user-plus text-primary"></i>
              </div>
              <div>
                <p class="mb-0">Nouvel utilisateur inscrit</p>
                <small class="text-muted">Il y a 2 heures</small>
              </div>
            </li>
            <li class="list-group-item d-flex align-items-center recent-item p-3">
              <div class="avatar-wrapper me-3 bg-success bg-opacity-10 rounded-circle p-2">
                <i class="fas fa-shopping-cart text-success"></i>
              </div>
              <div>
                <p class="mb-0">Nouvelle commande (#123456)</p>
                <small class="text-muted">Il y a 5 heures</small>
              </div>
            </li>
            <li class="list-group-item d-flex align-items-center recent-item p-3">
              <div class="avatar-wrapper me-3 bg-warning bg-opacity-10 rounded-circle p-2">
                <i class="fas fa-star text-warning"></i>
              </div>
              <div>
                <p class="mb-0">Nouvel avis sur l'album "Jazz Fusion"</p>
                <small class="text-muted">Il y a 1 jour</small>
              </div>
            </li>
            <li class="list-group-item d-flex align-items-center recent-item p-3">
              <div class="avatar-wrapper me-3 bg-info bg-opacity-10 rounded-circle p-2">
                <i class="fas fa-compact-disc text-info"></i>
              </div>
              <div>
                <p class="mb-0">Nouvel album ajouté: "Electronic Dreams"</p>
                <small class="text-muted">Il y a 2 jours</small>
              </div>
            </li>
          </ul>
        </div>
        <div class="card-footer bg-light">
          <a href="#" class="btn btn-sm btn-link d-block text-center">Voir toute l'activité</a>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  // Initialisation des tooltips et popovers
  document.addEventListener('DOMContentLoaded', function() {
    var tooltips = [].slice.call(document.querySelectorAll('[data-mdb-toggle="tooltip"]'));
    tooltips.map(function(tooltip) {
      return new mdb.Tooltip(tooltip);
    });
  });
</script>
@endsection

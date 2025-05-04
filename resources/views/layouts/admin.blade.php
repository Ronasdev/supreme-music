<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin - {{ config('app.name', 'Supreme Musique') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- MDB UI Kit -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet" />
  
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  
  <!-- Styles personnalisées pour l'admin -->
  <style>
    .admin-sidebar {
      width: 280px;
      height: 100vh;
      position: fixed;
      overflow-y: auto;
      background-color: #1a237e;
      color: white;
      transition: all 0.3s;
      z-index: 1000;
    }
    .admin-sidebar .logo {
      padding: 15px 0;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .admin-sidebar .nav-link {
      padding: 12px 20px;
      color: rgba(255, 255, 255, 0.8);
      border-left: 3px solid transparent;
      transition: all 0.2s;
    }
    .admin-sidebar .nav-link:hover, .admin-sidebar .nav-link.active {
      color: white;
      background-color: rgba(255, 255, 255, 0.1);
      border-left-color: #c2185b;
    }
    .admin-sidebar .nav-heading {
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255, 255, 255, 0.5);
      padding: 1.5rem 1.5rem 0.5rem;
    }
    .admin-main {
      margin-left: 280px;
      width: 100%;
      min-height: 100vh;
      background-color: #f5f5f5;
      transition: all 0.3s;
    }
    .admin-header {
      background-color: white;
      height: 60px;
    }
    @media (max-width: 768px) {
      .admin-sidebar {
        width: 60px;
      }
      .admin-sidebar .logo-text, .admin-sidebar .nav-text, .admin-sidebar .nav-heading {
        display: none;
      }
      .admin-sidebar .nav-link {
        text-align: center;
        padding: 15px 5px;
      }
      .admin-main {
        margin-left: 60px;
      }
    }
  </style>
  
  @yield('styles')
</head>
<body>

<div class="d-flex">
  <!-- Sidebar Admin -->
  <div class="admin-sidebar shadow">
    <div class="logo">
      <a href="{{ route('admin.dashboard') }}" class="d-block text-decoration-none">
        <h4 class="text-white mb-0">
          <i class="fas fa-music me-2"></i>
          <span class="logo-text">Supreme Admin</span>
        </h4>
      </a>
    </div>
    
    <div class="mt-3">
      <!-- Tableau de bord -->
      <a href="{{ route('admin.dashboard') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt fa-fw me-3"></i>
        <span class="nav-text">Tableau de bord</span>
      </a>
      
      <!-- Navigation Content -->
      <div class="nav-heading">Contenu</div>
      
      <!-- Albums -->
      <a href="{{ route('admin.albums.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.albums.*') ? 'active' : '' }}">
        <i class="fas fa-compact-disc fa-fw me-3"></i>
        <span class="nav-text">Albums</span>
      </a>
      
      <!-- Chansons -->
      <a href="{{ route('admin.songs.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.songs.*') ? 'active' : '' }}">
        <i class="fas fa-music fa-fw me-3"></i>
        <span class="nav-text">Chansons</span>
      </a>
      
      <!-- Playlists -->
      <a href="{{ route('admin.playlists.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.playlists.*') ? 'active' : '' }}">
        <i class="fas fa-list fa-fw me-3"></i>
        <span class="nav-text">Playlists</span>
      </a>
      
      <!-- Navigation Ventes -->
      <div class="nav-heading">Ventes</div>
      
      <!-- Commandes -->
      <a href="{{ route('admin.orders.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart fa-fw me-3"></i>
        <span class="nav-text">Commandes</span>
      </a>
      
      <!-- Paiements -->
      <a href="{{ route('admin.payments.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
        <i class="fas fa-credit-card fa-fw me-3"></i>
        <span class="nav-text">Paiements</span>
      </a>
      
      <!-- Navigation Utilisateurs -->
      <div class="nav-heading">Utilisateurs</div>
      
      <!-- Gestion des utilisateurs -->
      <a href="{{ route('admin.users.index') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <i class="fas fa-users fa-fw me-3"></i>
        <span class="nav-text">Utilisateurs</span>
      </a>
      
      <!-- Navigation Système -->
      <div class="nav-heading">Système</div>
      
      <!-- Paramètres -->
      <a href="{{ route('admin.settings') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
        <i class="fas fa-cog fa-fw me-3"></i>
        <span class="nav-text">Paramètres</span>
      </a>
      
      <!-- Statistiques -->
      <a href="{{ route('admin.stats') }}" class="nav-link d-flex align-items-center {{ request()->routeIs('admin.stats') ? 'active' : '' }}">
        <i class="fas fa-chart-bar fa-fw me-3"></i>
        <span class="nav-text">Statistiques</span>
      </a>
    </div>
  </div>

  <!-- Contenu principal -->
  <div class="admin-main">
    <!-- Header Admin -->
    <header class="admin-header shadow-sm bg-white">
      <div class="container-fluid h-100">
        <div class="d-flex justify-content-between align-items-center h-100">
          <div>
            <button class="btn btn-sm btn-light" id="sidebarToggle" type="button">
              <i class="fas fa-bars"></i>
            </button>
          </div>
          
          <div class="d-flex align-items-center">
            <div class="dropdown me-3">
              <a class="nav-link dropdown-toggle text-dark" href="#" role="button" id="notificationsDropdown" data-mdb-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span class="badge rounded-pill badge-notification bg-danger">1</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                <li><a class="dropdown-item" href="#">Nouvelle commande #12345</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center" href="#">Voir toutes les notifications</a></li>
              </ul>
            </div>
            
            <div class="dropdown">
              <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-profiles/avatar-2.webp" class="rounded-circle" height="25" width="25" alt="Portrait" loading="lazy" />
                <span class="ms-2 d-none d-sm-inline">{{ Auth::user()->name }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2"></i>Mon profil</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="fas fa-cog me-2"></i>Paramètres</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form method="POST" action="{{ route('logout') }}" class="dropdown-item">
                    @csrf
                    <button type="submit" class="btn btn-link p-0">
                      <i class="fas fa-sign-out-alt me-2"></i>Se déconnecter
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Contenu de la page -->
    <main class="p-4">
      @if(session('admin_success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('admin_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      
      @if(session('admin_error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('admin_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif
      
      @yield('content')
    </main>
  </div>
</div>

<script>
  // Toggle sidebar sur les petits écrans
  document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', function() {
        document.querySelector('.admin-sidebar').classList.toggle('toggled');
        document.querySelector('.admin-main').classList.toggle('expanded');
      });
    }
  });
</script>

@yield('scripts')
</body>
</html>

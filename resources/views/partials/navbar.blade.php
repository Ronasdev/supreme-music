<!-- Navbar principale -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container container-custom">
    <!-- Logo et titre -->
    <a class="navbar-brand" href="/">
      <i class="fas fa-headphones-alt me-2"></i> Supreme Musique
    </a>
    
    <!-- Bouton hamburger pour mobile -->
    <button class="navbar-toggler" type="button" data-mdb-toggle="collapse" data-mdb-target="#navbarNav">
      <i class="fas fa-bars"></i>
    </button>
    
    <!-- Navigation principale -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Liens publics (gauche) -->
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-home me-1"></i> Accueil
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('albums.index') }}">
            <i class="fas fa-compact-disc me-1"></i> Albums
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('songs.index') }}">
            <i class="fas fa-music me-1"></i> Titres
          </a>
        </li>
      </ul>
      
      <!-- Liens utilisateurs (droite) -->
      <ul class="navbar-nav mb-2 mb-lg-0">
        @auth
          <!-- Cart icon avec badge -->
          <li class="nav-item me-2">
            <a class="nav-link position-relative" href="{{ route('cart.show') }}" data-mdb-toggle="tooltip" title="Votre panier">
              <i class="fas fa-shopping-cart fa-lg"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ count(session('cart', [])) }}
              </span>
            </a>
          </li>
          
          <!-- Mes commandes -->
          <li class="nav-item me-2">
            <a class="nav-link" href="{{ route('orders.index') }}" data-mdb-toggle="tooltip" title="Vos commandes">
              <i class="fas fa-receipt fa-lg"></i>
            </a>
          </li>
          
          <!-- Menu Ma musique -->
          <li class="nav-item dropdown me-2">
            <a class="nav-link dropdown-toggle" href="#" id="libraryDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-headphones fa-lg me-1"></i> Ma musique
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-5" aria-labelledby="libraryDropdown">
              <li>
                <a class="dropdown-item" href="{{ route('library') }}">
                  <i class="fas fa-book-open me-2 text-primary-custom"></i> Ma bibliothèque
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('playlists.index') }}">
                  <i class="fas fa-list me-2 text-primary-custom"></i> Mes playlists
                </a>
              </li>
            </ul>
          </li>
          
          <!-- Section admin - Accès direct au dashboard -->
          @if(auth()->user()->is_admin)
            <li class="nav-item me-2">
              <a class="btn btn-sm btn-danger" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt me-1"></i> Dashboard
              </a>
            </li>
            <li class="nav-item dropdown me-2">
              <a class="nav-link dropdown-toggle admin-menu" href="#" id="adminDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-shield me-1"></i> Admin
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow-5" aria-labelledby="adminDropdown">
                <li>
                  <span class="dropdown-header">Administration</span>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt me-2 text-primary-custom"></i> Tableau de bord
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <span class="dropdown-header">Catalogue</span>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.albums.index') }}">
                    <i class="fas fa-compact-disc me-2 text-primary-custom"></i> Gestion des albums
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.songs.index') }}">
                    <i class="fas fa-music me-2 text-primary-custom"></i> Gestion des titres
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <span class="dropdown-header">Utilisateurs & Commandes</span>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-2 text-primary-custom"></i> Utilisateurs
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-clipboard-list me-2 text-primary-custom"></i> Commandes
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item" href="{{ route('admin.settings') }}">
                    <i class="fas fa-cog me-2 text-primary-custom"></i> Paramètres
                  </a>
                </li>
              </ul>
            </li>
          @endif
          
          <!-- Profil utilisateur -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
              <span class="d-none d-md-inline me-2">{{ auth()->user()->name }}</span>
              @if(session('user_avatar'))
                <img src="{{ session('user_avatar') }}" class="rounded-circle" height="25" width="25" style="object-fit: cover;" alt="{{ auth()->user()->name }}" loading="lazy">
              @elseif(auth()->user()->getFirstMediaUrl('avatar'))
                <img src="{{ auth()->user()->getFirstMediaUrl('avatar') }}" class="rounded-circle" height="25" width="25" style="object-fit: cover;" alt="{{ auth()->user()->name }}" loading="lazy">
              @elseif(file_exists(public_path('storage/avatars/'.auth()->id().'.jpg')))
                <img src="{{ asset('storage/avatars/'.auth()->id().'.jpg') }}" class="rounded-circle" height="25" width="25" style="object-fit: cover;" alt="{{ auth()->user()->name }}" loading="lazy">
              @else
                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="height: 25px; width: 25px;">
                  <span class="text-white" style="font-size: 12px; line-height: 1;">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
              @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-5" aria-labelledby="userDropdown">
              <li>
                <span class="dropdown-header">Mon compte</span>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                  <i class="fas fa-user-circle me-2 text-primary-custom"></i> Mon profil
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('orders.index') }}">
                  <i class="fas fa-receipt me-2 text-primary-custom"></i> Mes commandes
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item text-danger">
                    <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                  </button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <!-- Visiteurs non connectés -->
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">
              <i class="fas fa-sign-in-alt me-1"></i> Connexion
            </a>
          </li>
          <li class="nav-item">
            <a class="btn btn-sm btn-secondary ms-2" href="{{ route('register') }}">
              <i class="fas fa-user-plus me-1"></i> S'inscrire
            </a>
          </li>
        @endauth
      </ul>
    </div>
  </div>
</nav>
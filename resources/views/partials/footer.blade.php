<footer class="bg-primary-custom text-white">
    <!-- Section principale avec les colonnes -->
    <div class="container-custom py-5">
        <div class="row g-4">
            <!-- Logo et description -->
            <div class="col-lg-4 col-md-6">
                <h4 class="mb-4 d-flex align-items-center">
                    <i class="fas fa-headphones-alt me-3 fa-lg"></i>
                    <span class="fw-bold">Supreme Musique</span>
                </h4>
                <p class="mb-4 opacity-90">
                    Votre plateforme de vente et streaming de musique. Découvrez, achetez et écoutez votre musique préférée où que vous soyez.
                </p>
                <!-- Badges app stores -->
                <div class="d-flex gap-2 mb-4">
                    <a href="#" class="btn btn-outline-light btn-floating me-1" data-mdb-toggle="tooltip" title="App Store">
                        <i class="fab fa-apple"></i>
                    </a>
                    <a href="#" class="btn btn-outline-light btn-floating me-1" data-mdb-toggle="tooltip" title="Play Store">
                        <i class="fab fa-google-play"></i>
                    </a>
                </div>
            </div>

            <!-- Liens Explorer -->
            <div class="col-lg-2 col-md-6 col-sm-6">
                <h5 class="text-uppercase mb-4 fw-bold">Explorer</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-white opacity-80 hover-opacity-100">
                            <i class="fas fa-home me-2 opacity-70"></i>Accueil
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('albums.index') }}" class="text-white opacity-80 hover-opacity-100">
                            <i class="fas fa-compact-disc me-2 opacity-70"></i>Albums
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('songs.index') }}" class="text-white opacity-80 hover-opacity-100">
                            <i class="fas fa-music me-2 opacity-70"></i>Titres
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('how-it-works') }}" class="text-white opacity-80 hover-opacity-100">
                            <i class="fas fa-question-circle me-2 opacity-70"></i>Comment ça marche
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Liens Mon compte -->
            <div class="col-lg-2 col-md-6 col-sm-6">
                <h5 class="text-uppercase mb-4 fw-bold">Mon compte</h5>
                <ul class="list-unstyled mb-0">
                    @auth
                        <li class="mb-2">
                            <a href="{{ route('profile.edit') }}" class="text-white opacity-80 hover-opacity-100">
                                <i class="fas fa-user-circle me-2 opacity-70"></i>Profil
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('library') }}" class="text-white opacity-80 hover-opacity-100">
                                <i class="fas fa-book-open me-2 opacity-70"></i>Ma bibliothèque
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('playlists.index') }}" class="text-white opacity-80 hover-opacity-100">
                                <i class="fas fa-list me-2 opacity-70"></i>Mes playlists
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('orders.index') }}" class="text-white opacity-80 hover-opacity-100">
                                <i class="fas fa-receipt me-2 opacity-70"></i>Mes commandes
                            </a>
                        </li>
                    @else
                        <li class="mb-2">
                            <a href="{{ route('login') }}" class="text-white opacity-80 hover-opacity-100">
                                <i class="fas fa-sign-in-alt me-2 opacity-70"></i>Connexion
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('register') }}" class="text-white opacity-80 hover-opacity-100">
                                <i class="fas fa-user-plus me-2 opacity-70"></i>Inscription
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-uppercase mb-4 fw-bold">Contact</h5>
                <p class="mb-3">
                    <i class="fas fa-home me-3 opacity-70"></i>
                    123 Rue de la Musique, 75001 Paris
                </p>
                <p class="mb-3">
                    <i class="fas fa-envelope me-3 opacity-70"></i>
                    contact@supreme-musique.com
                </p>
                <p class="mb-4">
                    <i class="fas fa-phone me-3 opacity-70"></i>
                    + 33 1 23 45 67 89
                </p>
                <!-- Newsletter -->
                <h6 class="mb-3">Abonnez-vous à notre newsletter</h6>
                <div class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Votre email">
                    <button class="btn btn-secondary">S'abonner</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Section réseaux sociaux -->
    <div class="bg-primary-dark py-3">
        <div class="container-custom d-flex justify-content-between align-items-center flex-wrap">
            <!-- Réseaux sociaux -->
            <div class="d-flex me-3 mb-3 mb-md-0">
                <a href="#" class="btn btn-floating btn-sm btn-light mx-1" data-mdb-toggle="tooltip" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="btn btn-floating btn-sm btn-light mx-1" data-mdb-toggle="tooltip" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="btn btn-floating btn-sm btn-light mx-1" data-mdb-toggle="tooltip" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="btn btn-floating btn-sm btn-light mx-1" data-mdb-toggle="tooltip" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
            
            <!-- Copyright -->
            <div class="text-white">
                &copy; {{ date('Y') }} Supreme Musique - Tous droits réservés
            </div>
        </div>
    </div>

    <!-- Style spécifique pour le footer -->
    <style>
        .opacity-80 { opacity: 0.8; }
        .opacity-70 { opacity: 0.7; }
        .opacity-90 { opacity: 0.9; }
        .hover-opacity-100:hover { opacity: 1; transition: opacity 0.3s ease; }
    </style>
</footer>

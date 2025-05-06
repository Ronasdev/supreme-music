<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="font-bold text-xl text-purple-700 flex items-center">
                        <i class="fas fa-music mr-2"></i> Supreme Musique
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <!-- Liens de navigation publics (visibles par tous) -->
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        <i class="fas fa-home mr-1"></i> {{ __('Accueil') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('albums.index')" :active="request()->routeIs('albums.*')">
                        <i class="fas fa-compact-disc mr-1"></i> {{ __('Albums') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('songs.index')" :active="request()->routeIs('songs.*')">
                        <i class="fas fa-music mr-1"></i> {{ __('Chansons') }}
                    </x-nav-link>
                    
                    <!-- Navigation pour utilisateurs connectés -->
                    @auth
                        <x-nav-link :href="route('library')" :active="request()->routeIs('library')">
                            <i class="fas fa-headphones mr-1"></i> {{ __('Ma Bibliothèque') }}
                        </x-nav-link>
                        
                        <x-nav-link :href="route('playlists.index')" :active="request()->routeIs('playlists.*')">
                            <i class="fas fa-list-ul mr-1"></i> {{ __('Mes Playlists') }}
                        </x-nav-link>
                        
                        <!-- Navigation pour administrateurs uniquement -->
                        @if(Auth::user()->is_admin)
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" class="text-purple-600 font-bold">
                                <i class="fas fa-tachometer-alt mr-1"></i> {{ __('Administration') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Menu de droite (Authentification et panier) -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                <!-- Bouton panier (visible par tous) -->
                <a href="{{ route('cart.show') }}" class="px-3 py-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 relative">
                    <i class="fas fa-shopping-cart"></i>
                    @auth
                        <?php $cartCount = session()->get('cart') ? count(session()->get('cart')) : 0; ?>
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    @endauth
                </a>
                
                @auth
                    <!-- Menu déroulant pour utilisateur connecté -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <!-- Affiche le nom de l'utilisateur connecté -->
                                <div class="flex items-center">
                                    <i class="fas fa-user-circle mr-1 text-gray-400"></i>
                                    {{ Auth::user()->name }}
                                    @if(Auth::user()->is_admin)
                                        <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full">Admin</span>
                                    @endif
                                </div>

                                <!-- Icône de flèche vers le bas pour indiquer le dropdown -->
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Liens communs à tous les utilisateurs -->
                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="fas fa-user-cog mr-2"></i> {{ __('Mon profil') }}
                            </x-dropdown-link>
                            
                            <x-dropdown-link :href="route('orders.index')">
                                <i class="fas fa-shopping-bag mr-2"></i> {{ __('Mes commandes') }}
                            </x-dropdown-link>
                            
                            <!-- Liens d'administration uniquement pour les admins -->
                            @if(Auth::user()->is_admin)
                                <div class="border-t border-gray-200 my-1"></div>
                                
                                <x-dropdown-link :href="route('admin.dashboard')">
                                    <i class="fas fa-tachometer-alt mr-2"></i> {{ __('Tableau de bord') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('admin.albums.index')">
                                    <i class="fas fa-compact-disc mr-2"></i> {{ __('Gérer albums') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('admin.songs.index')">
                                    <i class="fas fa-music mr-2"></i> {{ __('Gérer chansons') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('admin.users.index')">
                                    <i class="fas fa-users mr-2"></i> {{ __('Gérer utilisateurs') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('admin.orders.index')">
                                    <i class="fas fa-shopping-cart mr-2"></i> {{ __('Commandes') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('admin.stats')">
                                    <i class="fas fa-chart-bar mr-2"></i> {{ __('Statistiques') }}
                                </x-dropdown-link>
                                
                                <x-dropdown-link :href="route('admin.settings')">
                                    <i class="fas fa-cogs mr-2"></i> {{ __('Paramètres') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-gray-200 my-1"></div>
                            
                            <!-- Déconnexion -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <!-- Boutons de connexion et d'inscription pour visiteurs -->
                    <div class="flex items-center space-x-3">
                        <!-- Bouton de connexion -->
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-sign-in-alt mr-2"></i> {{ __('Se connecter') }}
                        </a>
                        
                        <!-- Bouton d'inscription -->
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <i class="fas fa-user-plus mr-2"></i> {{ __('S\'inscrire') }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger pour mobile -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Menu de navigation responsive (mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <!-- Liens publics (version mobile) -->
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                <i class="fas fa-home mr-2"></i> {{ __('Accueil') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('albums.index')" :active="request()->routeIs('albums.*')">
                <i class="fas fa-compact-disc mr-2"></i> {{ __('Albums') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('songs.index')" :active="request()->routeIs('songs.*')">
                <i class="fas fa-music mr-2"></i> {{ __('Chansons') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('cart.show')" :active="request()->routeIs('cart.*')">
                <i class="fas fa-shopping-cart mr-2"></i> {{ __('Panier') }}
                @auth
                    <?php $cartCount = session()->get('cart') ? count(session()->get('cart')) : 0; ?>
                    @if($cartCount > 0)
                        <span class="ml-2 bg-red-500 text-white rounded-full text-xs px-2 py-0.5">{{ $cartCount }}</span>
                    @endif
                @endauth
            </x-responsive-nav-link>
            
            <!-- Liens pour utilisateurs connectés (version mobile) -->
            @auth
                <x-responsive-nav-link :href="route('library')" :active="request()->routeIs('library')">
                    <i class="fas fa-headphones mr-2"></i> {{ __('Ma Bibliothèque') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('playlists.index')" :active="request()->routeIs('playlists.*')">
                    <i class="fas fa-list-ul mr-2"></i> {{ __('Mes Playlists') }}
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                    <i class="fas fa-shopping-bag mr-2"></i> {{ __('Mes Commandes') }}
                </x-responsive-nav-link>
            @endauth
            
            <!-- Section admin (version mobile) -->
            @auth
                @if(Auth::user()->is_admin)
                    <div class="border-t border-gray-200 my-2"></div>
                    <div class="px-4 py-2 text-xs text-gray-500 font-semibold">ADMINISTRATION</div>
                    
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <i class="fas fa-tachometer-alt mr-2"></i> {{ __('Tableau de bord') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.albums.index')" :active="request()->routeIs('admin.albums.*')">
                        <i class="fas fa-compact-disc mr-2"></i> {{ __('Gérer albums') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.songs.index')" :active="request()->routeIs('admin.songs.*')">
                        <i class="fas fa-music mr-2"></i> {{ __('Gérer chansons') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        <i class="fas fa-users mr-2"></i> {{ __('Gérer utilisateurs') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                        <i class="fas fa-shopping-cart mr-2"></i> {{ __('Commandes') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.stats')" :active="request()->routeIs('admin.stats')">
                        <i class="fas fa-chart-bar mr-2"></i> {{ __('Statistiques') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('admin.settings')" :active="request()->routeIs('admin.settings')">
                        <i class="fas fa-cogs mr-2"></i> {{ __('Paramètres') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Options du compte (version mobile) -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            @auth
                <!-- Options pour utilisateur connecté (version mobile) -->
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-gray-400"></i>
                        {{ Auth::user()->name }}
                        @if(Auth::user()->is_admin)
                            <span class="ml-2 text-xs bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full">Admin</span>
                        @endif
                    </div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        <i class="fas fa-user-cog mr-2"></i> {{ __('Mon profil') }}
                    </x-responsive-nav-link>

                    <!-- Déconnexion (version mobile) -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Déconnexion') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <!-- Boutons d'authentification pour visiteurs (version mobile) -->
                <div class="mt-3 space-y-1 px-4">
                    <x-responsive-nav-link :href="route('login')">
                        <i class="fas fa-sign-in-alt mr-2"></i> {{ __('Se connecter') }}
                    </x-responsive-nav-link>
                    
                    <x-responsive-nav-link :href="route('register')">
                        <i class="fas fa-user-plus mr-2"></i> {{ __('S\'inscrire') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>

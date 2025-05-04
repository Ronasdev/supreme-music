<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Supreme Musique') }}</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    
    <!-- MDB UI Kit CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css" rel="stylesheet" />
    
    <!-- Styles personnalisés -->
    <style>
        :root {
            --primary-color: #6a1b9a;
            --primary-dark: #38006b;
            --primary-light: #9c4dcc;
            --secondary-color: #ff6e40;
            --secondary-dark: #c53d13;
            --secondary-light: #ffa06d;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f8f9fa;
        }
        
        main {
            flex: 1 0 auto;
        }
        
        .container-custom {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .navbar {
            background-color: var(--primary-color) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .navbar .dropdown-menu {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            border: none;
            border-radius: 8px;
        }
        
        .admin-menu {
            background-color: var(--secondary-color) !important;
            color: white !important;
            border-radius: 6px;
            padding: 0.5rem 1rem !important;
            margin-left: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        footer {
            background-color: #212121;
            color: white;
            padding: 2rem 0;
        }
        
        .card {
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color) !important;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color) !important;
        }
        
        .bg-primary-custom {
            background-color: var(--primary-color) !important;
        }
        
        .text-primary-custom {
            color: var(--primary-color) !important;
        }
    </style>
    
    <!-- Section pour des styles spécifiques à chaque page -->
    @yield('styles')
</head>

<body>
    <!-- Header Navigation -->
    @include('partials.navbar')
    
    <!-- Main Content -->
    <main>
        <div class="container-custom py-4">
            <!-- Messages flash -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Contenu principal -->
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    @include('partials.footer')
    
    <!-- MDB JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"></script>
    
    <!-- Initialisation des composants MDB -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation des dropdowns
            document.querySelectorAll('.dropdown-toggle').forEach(function(element) {
                new mdb.Dropdown(element);
            });
            
            // Initialisation du menu hamburger (toggler)
            var toggler = document.querySelector('.navbar-toggler');
            if (toggler) {
                new mdb.Collapse(document.getElementById('navbarNav'), {
                    toggle: false
                });
            }
            
            // Initialisation des tooltips
            document.querySelectorAll('[data-mdb-toggle="tooltip"]').forEach(function(element) {
                new mdb.Tooltip(element);
            });
            
            // Initialisation des popovers
            document.querySelectorAll('[data-mdb-toggle="popover"]').forEach(function(element) {
                new mdb.Popover(element);
            });
            
            // Initialisation des alertes dismissible
            document.querySelectorAll('.alert-dismissible').forEach(function(element) {
                new mdb.Alert(element);
            });
        });
    </script>
    
    <!-- Section pour des scripts spécifiques à chaque page -->
    @yield('scripts')
</body>

</html>
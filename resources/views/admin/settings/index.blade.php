@extends('layouts.admin')

@section('content')
<h2>
    <i class="fas fa-cogs me-2"></i>Paramètres du Site
</h2>

@if(session('admin_success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i>{{ session('admin_success') }}
</div>
@endif

@if(session('admin_error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('admin_error') }}
</div>
@endif

<div class="row">
    <!-- Paramètres généraux -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Paramètres généraux</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Nom du site</label>
                        <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']->value ?? 'Supreme Musique') }}">
                        @error('site_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_description" class="form-label">Description du site</label>
                        <textarea class="form-control @error('site_description') is-invalid @enderror" id="site_description" name="site_description" rows="2">{{ old('site_description', $settings['site_description']->value ?? 'Votre plateforme de musique en ligne') }}</textarea>
                        @error('site_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email de contact</label>
                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']->value ?? 'contact@suprememusique.com') }}">
                        @error('contact_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="default_currency" class="form-label">Devise par défaut</label>
                        <select class="form-select @error('default_currency') is-invalid @enderror" id="default_currency" name="default_currency">
                            <option value="EUR" {{ (old('default_currency', $settings['default_currency']->value ?? 'EUR') == 'EUR') ? 'selected' : '' }}>Euro (€)</option>
                            <option value="USD" {{ (old('default_currency', $settings['default_currency']->value ?? 'EUR') == 'USD') ? 'selected' : '' }}>Dollar US ($)</option>
                            <option value="GBP" {{ (old('default_currency', $settings['default_currency']->value ?? 'EUR') == 'GBP') ? 'selected' : '' }}>Livre Sterling (£)</option>
                            <option value="XOF" {{ (old('default_currency', $settings['default_currency']->value ?? 'EUR') == 'XOF') ? 'selected' : '' }}>Franc CFA (FCFA)</option>
                        </select>
                        @error('default_currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enable_user_registration" name="enable_user_registration" value="1" {{ old('enable_user_registration', $settings['enable_user_registration']->value ?? '1') ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_user_registration">Autoriser les inscriptions d'utilisateurs</label>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="enable_guest_checkout" name="enable_guest_checkout" value="1" {{ old('enable_guest_checkout', $settings['enable_guest_checkout']->value ?? '0') ? 'checked' : '' }}>
                        <label class="form-check-label" for="enable_guest_checkout">Autoriser les achats sans compte</label>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode']->value ?? '0') ? 'checked' : '' }}>
                        <label class="form-check-label" for="maintenance_mode">Mode maintenance</label>
                        <div class="form-text text-danger">Attention : activer ce mode rendra le site inaccessible aux visiteurs.</div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer les paramètres
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Informations système -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Informations système</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Laravel
                        <span class="badge bg-info">{{ $appInfo['laravel_version'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        PHP
                        <span class="badge bg-info">{{ $appInfo['php_version'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Environnement
                        <span class="badge bg-{{ $appInfo['environment'] == 'production' ? 'success' : 'warning' }}">{{ $appInfo['environment'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Cache
                        <span class="badge bg-secondary">{{ $appInfo['cache_driver'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Session
                        <span class="badge bg-secondary">{{ $appInfo['session_driver'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Base de données
                        <span class="badge bg-secondary">{{ $appInfo['database'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h3 class="h5 mb-0">Stockage</h3>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Taille du disque public
                        <span class="badge bg-info">{{ $storageInfo['public_disk_size'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Fichiers média (total)
                        <span class="badge bg-info">{{ $storageInfo['total_media_files'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Taille totale des médias
                        <span class="badge bg-info">{{ $storageInfo['total_media_size'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Fichiers audio
                        <span class="badge bg-primary">{{ $storageInfo['audio_files'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Taille des fichiers audio
                        <span class="badge bg-primary">{{ $storageInfo['audio_size'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Images de couverture
                        <span class="badge bg-success">{{ $storageInfo['cover_files'] }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Taille des images de couverture
                        <span class="badge bg-success">{{ $storageInfo['cover_size'] }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-light">
        <h3 class="h5 mb-0">Maintenance du système</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Cache</h5>
                        <p class="card-text">Vider le cache de l'application pour appliquer les dernières modifications.</p>
                        <form action="{{ route('admin.settings.maintenance') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="clear_cache">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-trash-alt me-2"></i>Vider le cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Vues</h5>
                        <p class="card-text">Vider le cache des vues pour appliquer les derniers changements de templates.</p>
                        <form action="{{ route('admin.settings.maintenance') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="clear_view_cache">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-trash-alt me-2"></i>Vider le cache des vues
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Optimisation</h5>
                        <p class="card-text">Optimiser l'application pour améliorer les performances.</p>
                        <form action="{{ route('admin.settings.maintenance') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="optimize">
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="fas fa-bolt me-2"></i>Optimiser
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

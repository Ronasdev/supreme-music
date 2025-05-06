@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Menu latéral -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(session('user_avatar'))
                            <!-- Affiche l'avatar depuis la session (nouvelle méthode) -->
                            <img src="{{ session('user_avatar') }}" class="rounded-circle img-fluid mx-auto shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="{{ Auth::user()->name }}">
                        @elseif(Auth::user()->hasAvatar())
                            <!-- Affiche l'avatar avec la nouvelle méthode -->
                            <img src="{{ Auth::user()->getAvatarUrl() }}" class="rounded-circle img-fluid mx-auto shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="{{ Auth::user()->name }}">
                        @elseif(file_exists(public_path('storage/avatars/'.Auth::id().'.jpg')))
                            <!-- Vérifie si un fichier avatar existe avec l'ID de l'utilisateur -->
                            <img src="{{ asset('storage/avatars/'.Auth::id().'.jpg') }}" class="rounded-circle img-fluid mx-auto shadow" style="width: 120px; height: 120px; object-fit: cover;" alt="{{ Auth::user()->name }}">
                        @else
                            <!-- Avatar par défaut avec l'initiale de l'utilisateur -->
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mx-auto shadow" style="width: 120px; height: 120px;">
                                <span class="text-white h1">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small">Membre depuis {{ Auth::user()->created_at->format('M Y') }}</p>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile-info" class="list-group-item list-group-item-action active" data-mdb-toggle="list">
                        <i class="fas fa-user-circle me-2"></i> Informations personnelles
                    </a>
                    <a href="#security" class="list-group-item list-group-item-action" data-mdb-toggle="list">
                        <i class="fas fa-lock me-2"></i> Sécurité
                    </a>
                    <a href="{{ route('library') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-music me-2"></i> Ma bibliothèque
                    </a>
                    <a href="{{ route('playlists.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-list me-2"></i> Mes playlists
                    </a>
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-receipt me-2"></i> Mes commandes
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Contenu principal -->
        <div class="col-lg-9">
            <div class="tab-content">
                <!-- Section Informations personnelles -->
                <div class="tab-pane fade show active" id="profile-info">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Informations personnelles</h5>
                        </div>
                        <div class="card-body">
                            @if (session('status') === 'profile-updated')
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i> Vos informations ont été mises à jour avec succès.
                                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-outline mb-4">
                                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required />
                                            <label class="form-label" for="name">Nom complet</label>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-outline mb-4">
                                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required />
                                            <label class="form-label" for="email">Adresse email</label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label d-block">Photo de profil</label>
                                    <input type="file" name="avatar" id="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*" />
                                    <small class="form-text text-muted">Formats acceptés : JPG, PNG. Taille max : 2 Mo</small>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Section Sécurité -->
                <div class="tab-pane fade" id="security">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Changer de mot de passe</h5>
                        </div>
                        <div class="card-body">
                            @if (session('status') === 'password-updated')
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i> Votre mot de passe a été mis à jour avec succès.
                                    <button type="button" class="btn-close" data-mdb-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <form method="post" action="{{ route('password.update') }}">
                                @csrf
                                @method('put')
                                
                                <div class="form-outline mb-4">
                                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required />
                                    <label class="form-label" for="current_password">Mot de passe actuel</label>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-outline mb-4">
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required />
                                    <label class="form-label" for="password">Nouveau mot de passe</label>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-outline mb-4">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required />
                                    <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-lock me-2"></i> Mettre à jour le mot de passe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm border-danger">
                        <div class="card-header bg-danger bg-opacity-10 text-danger">
                            <h5 class="mb-0">Supprimer mon compte</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">Une fois votre compte supprimé, toutes vos ressources et données seront définitivement effacées. Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.</p>
                            
                            <button type="button" class="btn btn-outline-danger" data-mdb-toggle="modal" data-mdb-target="#deleteAccountModal">
                                <i class="fas fa-trash-alt me-2"></i> Supprimer mon compte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression de compte -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-body">
                    <p class="mb-3">Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible et toutes vos données seront perdues.</p>
                    
                    <div class="form-outline mb-4">
                        <input type="password" id="password" name="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" required />
                        <label class="form-label" for="password">Mot de passe actuel</label>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-mdb-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

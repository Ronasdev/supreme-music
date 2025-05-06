<section>
    <header>
        <h2 class="fs-4 fw-bold mb-3">
            Informations personnelles
        </h2>

        <p class="text-muted mb-4">
            Mettez à jour vos informations personnelles et votre photo de profil.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mb-5" enctype="multipart/form-data">
        @csrf
        @method('patch')
        
        <!-- Avatar Upload -->
        <div class="mb-4">
            <label for="avatar" class="form-label fw-bold">Photo de profil</label>
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    @if(session('user_avatar'))
                        <img src="{{ session('user_avatar') }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    @elseif(Auth::user()->hasAvatar())
                        <img src="{{ Auth::user()->getAvatarUrl() }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <span class="text-white h4 mb-0">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                    <div class="form-text">Formats acceptés : JPG, PNG. Taille max : 2 Mo</div>
                    @if($errors->has('avatar'))
                        <div class="text-danger mt-1">{{ $errors->first('avatar') }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Nom -->
        <div class="mb-4">
            <label for="name" class="form-label fw-bold">Nom</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @if($errors->has('name'))
                <div class="text-danger mt-1">{{ $errors->first('name') }}</div>
            @endif
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="form-label fw-bold">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @if($errors->has('email'))
                <div class="text-danger mt-1">{{ $errors->first('email') }}</div>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2">
                    <p class="mb-1">
                        Votre adresse email n'est pas vérifiée.
                        <button form="send-verification" class="btn btn-link p-0 align-baseline">
                            Cliquez ici pour renvoyer l'email de vérification.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2">
                            Un nouveau lien de vérification a été envoyé à votre adresse email.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Boutons -->
        <div class="d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Enregistrer les modifications
            </button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success d-inline-block ms-3 mb-0 py-2">
                    <i class="fas fa-check me-2"></i> Profil mis à jour avec succès
                </div>
            @endif
        </div>
    </form>
</section>

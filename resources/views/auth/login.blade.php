<x-guest-layout>
    @php
        $title = 'Connexion';
    @endphp
    
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-outline mb-4">
            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
            <label class="form-label" for="email">{{ __('Adresse email') }}</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" />
            <label class="form-label" for="password">{{ __('Mot de passe') }}</label>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Additional options -->
        <div class="row mb-4">
            <div class="col-md-6">
                <!-- Remember Me -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember_me">{{ __('Se souvenir de moi') }}</label>
                </div>
            </div>
            <div class="col-md-6 text-end">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-primary">{{ __('Mot de passe oublié?') }}</a>
                @endif
            </div>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary btn-block shadow-0 mb-4">{{ __('Se connecter') }}</button>

        <!-- Register link -->
        <div class="text-center">
            <p>{{ __('Pas encore membre?') }} <a href="{{ route('register') }}" class="text-primary fw-bold">{{ __('Créer un compte') }}</a></p>
        </div>

        <!-- Social login -->
        <div class="divider d-flex align-items-center my-4">
            <p class="text-center mx-3 mb-0 text-muted">{{ __('Ou connectez-vous avec') }}</p>
        </div>

        <div class="d-flex justify-content-center mb-2">
            <a href="#" class="btn btn-floating btn-secondary mx-1">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="btn btn-floating btn-secondary mx-1">
                <i class="fab fa-google"></i>
            </a>
            <a href="#" class="btn btn-floating btn-secondary mx-1">
                <i class="fab fa-twitter"></i>
            </a>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    @php
        $title = 'Inscription';
    @endphp

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-outline mb-4">
            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            <label class="form-label" for="name">{{ __('Nom complet') }}</label>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="form-outline mb-4">
            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username" />
            <label class="form-label" for="email">{{ __('Adresse email') }}</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" />
            <label class="form-label" for="password">{{ __('Mot de passe') }}</label>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-outline mb-4">
            <input type="password" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" />
            <label class="form-label" for="password_confirmation">{{ __('Confirmer le mot de passe') }}</label>
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Terms and conditions -->
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="terms" name="terms" required />
            <label class="form-check-label" for="terms">
                {{ __('J\'accepte les') }} <a href="#" class="text-primary">{{ __('conditions d\'utilisation') }}</a>
            </label>
        </div>

        <!-- Submit button -->
        <button type="submit" class="btn btn-primary btn-block shadow-0 mb-4">{{ __('S\'inscrire') }}</button>

        <!-- Login link -->
        <div class="text-center">
            <p>{{ __('Déjà inscrit?') }} <a href="{{ route('login') }}" class="text-primary fw-bold">{{ __('Se connecter') }}</a></p>
        </div>

        <!-- Social signup -->
        <div class="divider d-flex align-items-center my-4">
            <p class="text-center mx-3 mb-0 text-muted">{{ __('Ou inscrivez-vous avec') }}</p>
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

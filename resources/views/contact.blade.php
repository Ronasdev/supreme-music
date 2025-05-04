@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">Contactez-nous</h1>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="lead mb-4">Vous avez des questions ou besoin d'assistance ? N'hésitez pas à nous contacter via le formulaire ci-dessous.</p>
                    
                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Votre nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Votre email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Sujet</label>
                            <select class="form-select @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                <option value="" selected disabled>Sélectionnez un sujet</option>
                                <option value="question" {{ old('subject') == 'question' ? 'selected' : '' }}>Question générale</option>
                                <option value="support" {{ old('subject') == 'support' ? 'selected' : '' }}>Support technique</option>
                                <option value="payment" {{ old('subject') == 'payment' ? 'selected' : '' }}>Problème de paiement</option>
                                <option value="suggestion" {{ old('subject') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                                <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Autre</option>
                            </select>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">Votre message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Envoyer le message</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4 shadow-sm">
                <div class="card-body">
                    <h2 class="h4 mb-3">Autres moyens de nous contacter</h2>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="fas fa-envelope text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">Email</h3>
                                    <p class="mb-0">contact@suprememusique.com</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="fas fa-phone text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">Téléphone</h3>
                                    <p class="mb-0">+XXX XXX XXX XXX</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-3 me-3">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="h6 mb-0">Adresse</h3>
                                    <p class="mb-0">1234 Rue de la Musique, Ville</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

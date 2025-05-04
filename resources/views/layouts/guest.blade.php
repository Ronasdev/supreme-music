@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-4 border-0">
                <div class="card-header bg-primary-custom text-white text-center py-4">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <i class="fas fa-headphones-alt fa-2x me-2"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ isset($title) ? $title : config('app.name', 'Supreme Musique') }}</h3>
                </div>
                <div class="card-body p-5">
                    {{ $slot }}
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">&copy; {{ date('Y') }} Supreme Musique - Tous droits réservés</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

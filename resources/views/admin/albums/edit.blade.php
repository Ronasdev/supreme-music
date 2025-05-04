@extends('layouts.admin')

@section('content')
<div class="d-flex align-items-center mb-4">
    <h2><i class="fas fa-edit me-2"></i>Modifier l'album</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('admin.albums.update', $album) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

    <div class="mb-3">
                <label for="title" class="form-label"><i class="fas fa-heading me-1"></i> Titre</label>
                <input type="text" name="title" class="form-control" value="{{ $album->title }}" required>
            </div>

            <div class="mb-3">
                <label for="artist" class="form-label"><i class="fas fa-user me-1"></i> Artiste</label>
                <input type="text" name="artist" class="form-control" value="{{ $album->artist }}" required>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label"><i class="fas fa-tag me-1"></i> Prix</label>
                <div class="input-group">
                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ $album->price }}" required>
                    <span class="input-group-text">€</span>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label"><i class="fas fa-align-left me-1"></i> Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $album->description }}</textarea>
            </div>

            <div class="mb-4">
                <label for="cover" class="form-label"><i class="fas fa-image me-1"></i> Image de couverture</label>
                <input type="file" name="cover" class="form-control" accept="image/*">
                <small class="text-muted">Laissez vide pour conserver l'image actuelle</small>
                
                @if($album->cover)
                <div class="mt-3">
                    <p class="mb-1">Image actuelle:</p>
                    <img src="{{ asset('storage/' . $album->cover) }}" class="img-thumbnail" style="max-width: 200px;">
                </div>
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.albums.index') }}" class="btn btn-light">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Modifier la chanson</h2>
        <a href="{{ route('admin.songs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.songs.update', $song) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $song->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Album -->
                        <div class="mb-3">
                            <label for="album_id" class="form-label">Album</label>
                            <select class="form-select @error('album_id') is-invalid @enderror" id="album_id" name="album_id">
                                <option value="">-- Aucun album --</option>
                                @foreach($albums as $album)
                                    <option value="{{ $album->id }}" {{ old('album_id', $song->album_id) == $album->id ? 'selected' : '' }}>{{ $album->title }} ({{ $album->artist }})</option>
                                @endforeach
                            </select>
                            @error('album_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Artiste (si différent de l'album) -->
                        <div class="mb-3">
                            <label for="artist" class="form-label">Artiste (si différent de l'album)</label>
                            <input type="text" class="form-control @error('artist') is-invalid @enderror" id="artist" name="artist" value="{{ old('artist', $song->artist) }}">
                            @error('artist')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Prix -->
                        <div class="mb-3">
                            <label for="price" class="form-label">Prix (€) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $song->price) }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <!-- Durée -->
                        <div class="mb-3">
                            <label for="duration" class="form-label">Durée (mm:ss)</label>
                            <input type="text" class="form-control @error('duration') is-invalid @enderror" id="duration" name="duration" value="{{ old('duration', $song->duration) }}" placeholder="03:45">
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Année -->
                        <div class="mb-3">
                            <label for="year" class="form-label">Année de sortie</label>
                            <input type="number" min="1900" max="{{ date('Y') }}" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year', $song->year) }}">
                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Fichier audio -->
                        <div class="mb-3">
                            <label for="audio_file" class="form-label">Fichier audio</label>
                            <input type="file" class="form-control @error('audio_file') is-invalid @enderror" id="audio_file" name="audio_file" accept="audio/*">
                            @error('audio_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if($song->hasAudioFile())
                                <div class="mt-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">Fichier actuel</span>
                                        <span class="text-truncate">{{ $song->audio_file }}</span>
                                    </div>
                                    <div class="form-text">Laissez vide pour conserver le fichier existant</div>
                                </div>
                            @else
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i> Aucun fichier audio associé
                                </div>
                            @endif
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $song->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Enregistrer les modifications
                    </button>
                    <a href="{{ route('admin.songs.index') }}" class="btn btn-outline-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

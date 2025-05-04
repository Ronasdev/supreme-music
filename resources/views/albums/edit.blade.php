@extends('layouts.app')

@section('content')
<div class="container">
  <h2 class="mb-4">Modifier l’album : {{ $album->title }}</h2>

  {{-- Affichage des erreurs de validation --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('albums.update', $album) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="form-outline mb-4">
      <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $album->title) }}" required />
      <label class="form-label" for="title">Titre de l’album</label>
    </div>

    <div class="form-outline mb-4">
      <textarea id="description" name="description" class="form-control" rows="4" required>{{ old('description', $album->description) }}</textarea>
      <label class="form-label" for="description">Description</label>
    </div>

    <div class="form-outline mb-4">
      <input type="number" id="price" name="price" class="form-control" value="{{ old('price', $album->price) }}" step="100" min="0" required />
      <label class="form-label" for="price">Prix (FCFA)</label>
    </div>

    <div class="mb-4">
      <label for="cover" class="form-label">Changer l’image de couverture (optionnel)</label>
      <input type="file" class="form-control" name="cover" id="cover">
      @if ($album->getFirstMediaUrl('cover'))
        <div class="mt-2">
          <strong>Image actuelle :</strong><br>
          <img src="{{ $album->getFirstMediaUrl('cover') }}" alt="cover" class="img-fluid rounded" style="max-height: 150px;">
        </div>
      @endif
    </div>

    <button type="submit" class="btn btn-success">Mettre à jour</button>
    <a href="{{ route('albums.index') }}" class="btn btn-secondary">Annuler</a>
  </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
  <h2 class="mb-4">Ajouter un nouvel album</h2>

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

  <form action="{{ route('albums.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-outline mb-4">
      <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required />
      <label class="form-label" for="title">Titre de l’album</label>
    </div>

    <div class="form-outline mb-4">
      <textarea id="description" name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
      <label class="form-label" for="description">Description</label>
    </div>

    <div class="form-outline mb-4">
      <input type="number" id="price" name="price" class="form-control" value="{{ old('price') }}" step="100" min="0" required />
      <label class="form-label" for="price">Prix (FCFA)</label>
    </div>

    <div class="mb-4">
      <label for="cover" class="form-label">Image de couverture</label>
      <input type="file" class="form-control" name="cover" id="cover" required>
    </div>

    <button type="submit" class="btn btn-primary">Créer l’album</button>
  </form>
</div>
@endsection

@extends('layouts.admin')

@section('content')
<h2>Liste des Albums 🎼</h2>

<a href="{{ route('albums.create') }}" class="btn btn-primary mb-3">➕ Ajouter un album</a>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Pochette</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    @foreach($albums as $album)
        <tr>
            <td>{{ $album->id }}</td>
            <td>{{ $album->title }}</td>
            <td>
                @if($album->cover)
                    <img src="{{ asset('storage/' . $album->cover) }}" width="60">
                @else
                    <em>Pas d’image</em>
                @endif
            </td>
            <td>
                <a href="{{ route('albums.edit', $album) }}" class="btn btn-sm btn-warning">✏️</a>
                <form action="{{ route('albums.destroy', $album) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">🗑️</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $albums->links() }}
@endsection

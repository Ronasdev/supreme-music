@extends('layouts.admin')

@section('content')
<h2>
    <i class="fas fa-compact-disc me-2"></i>Liste des Albums
</h2>

<a href="{{ route('admin.albums.create') }}" class="btn btn-primary mb-3">
    <i class="fas fa-plus me-2"></i>Ajouter un album
</a>

@if(session('admin_success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle me-2"></i>{{ session('admin_success') }}
</div>
@endif

@if(session('admin_error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle me-2"></i>{{ session('admin_error') }}
</div>
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
                @if($album->hasCoverImage())
                    <img src="{{ $album->getCoverImageUrl() }}" width="60">
                @else
                    <em>Pas d’image</em>
                @endif
            </td>
            <td>
                <a href="{{ route('admin.albums.edit', $album) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.albums.destroy', $album) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{ $albums->links() }}
@endsection

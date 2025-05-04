@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Chansons 🎵</h2>
        <a href="{{ route('admin.songs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Ajouter une chanson
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Album</th>
                            <th>Artiste</th>
                            <th>Prix</th>
                            <th>Durée</th>
                            <th>Fichier audio</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($songs as $song)
                        <tr>
                            <td>{{ $song->id }}</td>
                            <td>{{ $song->title }}</td>
                            <td>
                                @if($song->album)
                                    <a href="{{ route('admin.albums.edit', $song->album) }}" class="text-decoration-none">
                                        {{ $song->album->title }}
                                    </a>
                                @else
                                    <span class="text-muted">Aucun album</span>
                                @endif
                            </td>
                            <td>{{ $song->artist ?? ($song->album->artist ?? 'Non défini') }}</td>
                            <td>{{ number_format($song->price, 2) }} €</td>
                            <td>{{ $song->duration ?? '00:00' }}</td>
                            <td>
                                @if($song->getFirstMedia('audio'))
                                    <span class="badge bg-success">Fichier présent</span>
                                @else
                                    <span class="badge bg-danger">Manquant</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.songs.edit', $song) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.songs.show', $song) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.songs.destroy', $song) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette chanson ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <p class="text-muted mb-0">Aucune chanson trouvée</p>
                                <a href="{{ route('admin.songs.create') }}" class="btn btn-sm btn-primary mt-3">
                                    <i class="fas fa-plus-circle me-1"></i> Ajouter une chanson
                                </a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            {{ $songs->links() }}
        </div>
    </div>
</div>
@endsection

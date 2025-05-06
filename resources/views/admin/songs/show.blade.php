@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Détails de la chanson</h2>
        <div>
            <a href="{{ route('admin.songs.edit', $song) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="{{ route('admin.songs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="card-title h5 mb-0">Informations</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 150px">ID</th>
                                    <td>{{ $song->id }}</td>
                                </tr>
                                <tr>
                                    <th>Titre</th>
                                    <td>{{ $song->title }}</td>
                                </tr>
                                <tr>
                                    <th>Album</th>
                                    <td>
                                        @if($song->album)
                                            <a href="{{ route('admin.albums.show', $song->album) }}" class="text-decoration-none">
                                                {{ $song->album->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">Aucun album</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Artiste</th>
                                    <td>{{ $song->artist ?? ($song->album->artist ?? 'Non défini') }}</td>
                                </tr>
                                <tr>
                                    <th>Prix</th>
                                    <td>{{ number_format($song->price, 2) }} €</td>
                                </tr>
                                <tr>
                                    <th>Durée</th>
                                    <td>{{ $song->duration ?? 'Non définie' }}</td>
                                </tr>
                                <tr>
                                    <th>Année</th>
                                    <td>{{ $song->year ?? 'Non définie' }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $song->description ?? 'Aucune description' }}</td>
                                </tr>
                                <tr>
                                    <th>Date de création</th>
                                    <td>{{ $song->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Dernière modification</th>
                                    <td>{{ $song->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Fichier audio -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="card-title h5 mb-0">Fichier audio</h3>
                </div>
                <div class="card-body">
                    @if($song->hasAudioFile())
                        <div class="mb-3">
                            <audio controls class="w-100">
                                <source src="{{ $song->getAudioUrl() }}" type="audio/mpeg">
                                Votre navigateur ne supporte pas la lecture audio.
                            </audio>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small text-muted">
                            <span>{{ $song->audio_file }}</span>
                            <span>{{ $song->filesize ? round($song->filesize / 1024 / 1024, 2) . ' Mo' : 'Taille inconnue' }}</span>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i> Aucun fichier audio n'est associé à cette chanson.
                        </div>
                        <a href="{{ route('admin.songs.edit', $song) }}" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-upload me-1"></i> Ajouter un fichier audio
                        </a>
                    @endif
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h3 class="card-title h5 mb-0">Statistiques</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Nombre d'achats</span>
                            <span class="badge bg-primary rounded-pill">{{ $song->orderItems->count() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Nombre d'écoutes</span>
                            <span class="badge bg-info rounded-pill">{{ $song->streams_count ?? 0 }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Dans playlists</span>
                            <span class="badge bg-success rounded-pill">{{ $song->playlists->count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h3 class="card-title h5 mb-0">Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('songs.show', $song) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i> Voir page publique
                        </a>
                        <form action="{{ route('admin.songs.destroy', $song) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette chanson ? Cette action est irréversible.')">
                                <i class="fas fa-trash me-1"></i> Supprimer cette chanson
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<div class="mini-player card shadow-sm">
    <div class="card-body p-2">
        <div class="d-flex align-items-center">
            <!-- Image de couverture -->
            <div class="me-3">
                @if($song->album && $song->album->getFirstMedia('cover'))
                    <img src="{{ $song->album->getFirstMediaUrl('cover') }}" alt="{{ $song->title }}" style="width: 50px; height: 50px;" class="rounded shadow-sm">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-music text-muted"></i>
                    </div>
                @endif
            </div>
            
            <!-- Titre et artiste -->
            <div class="flex-grow-1 me-3">
                <h6 class="mb-0 text-truncate">{{ $song->title }}</h6>
                <small class="text-muted">{{ $song->artist ?? ($song->album ? $song->album->artist : 'Artiste inconnu') }}</small>
            </div>
            
            <!-- Contrôles du lecteur -->
            <div class="d-flex align-items-center">
                <audio id="mini-audio-{{ $song->id }}" src="{{ route('stream.audio', $song) }}" style="display: none;"></audio>
                <button class="btn btn-sm btn-primary rounded-circle me-2 mini-play-btn" data-song-id="{{ $song->id }}">
                    <i class="fas fa-play"></i>
                </button>
                <a href="{{ route('stream.play', $song) }}" class="btn btn-sm btn-outline-primary rounded-circle" title="Voir en plein écran">
                    <i class="fas fa-expand"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mini player functionality
        document.querySelectorAll('.mini-play-btn').forEach(button => {
            button.addEventListener('click', function() {
                const songId = this.dataset.songId;
                const audio = document.getElementById('mini-audio-' + songId);
                const icon = this.querySelector('i');
                
                if (audio.paused) {
                    // Pause all other players first
                    document.querySelectorAll('audio').forEach(a => {
                        if (a.id !== 'mini-audio-' + songId && !a.paused) {
                            a.pause();
                            const otherBtn = document.querySelector(`.mini-play-btn[data-song-id="${a.id.replace('mini-audio-', '')}"]`);
                            if (otherBtn) {
                                otherBtn.querySelector('i').classList.remove('fa-pause');
                                otherBtn.querySelector('i').classList.add('fa-play');
                            }
                        }
                    });
                    
                    // Play this audio
                    audio.play();
                    icon.classList.remove('fa-play');
                    icon.classList.add('fa-pause');
                } else {
                    // Pause this audio
                    audio.pause();
                    icon.classList.remove('fa-pause');
                    icon.classList.add('fa-play');
                }
            });
        });
    });
</script>

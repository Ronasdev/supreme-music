<?php

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    /**
     * Génère le chemin relatif pour un média
     *
     * @param Media $media
     *
     * @return string
     */
    public function getPath(Media $media): string
    {
        // Cette méthode est appelée pour déterminer où stocker physiquement le fichier
        // Important: nous ajoutons un séparateur pour que l'ID et le nom de fichier soient séparés
        return $media->id . '/';
    }

    /**
     * Génère le chemin relatif vers le dossier de conversion d'un média
     *
     * @param Media $media
     *
     * @return string
     */
    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . 'conversions';
    }

    /**
     * Génère le chemin relatif vers le dossier de réactivité d'un média
     *
     * @param Media $media
     *
     * @return string
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . 'responsive-images';
    }
}
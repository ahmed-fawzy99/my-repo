<?php

namespace App\Services\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CustomPathGenerator implements \Spatie\MediaLibrary\Support\PathGenerator\PathGenerator
{

    public function getPath(Media $media): string
    {
        return md5($media->id . $media->model_id) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getPath($media) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media) . '/responsive-images/';
    }
}

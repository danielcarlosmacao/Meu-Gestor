<?php

namespace App\Services;
use Illuminate\Support\Str;
use App\Models\TowerGallery;
class ImageService
{

    public function saveImages($images, $towerId)
    {
        foreach ($images as $image) {

            $path = $image->store('towers', 'public');

            $isImage = Str::startsWith($image->getMimeType(), 'image/');

            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

            $shortName = Str::limit($originalName, 25, '');

            $title = $isImage ? null : 'file:' . $shortName;

            TowerGallery::create([
                'tower_id' => $towerId,
                'path' => $path,
                'title' => $title,
            ]);
        }
    }
}

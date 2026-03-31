<?php

namespace App\Services;

use App\Models\TowerGallery;
class ImageService
{

      public function saveImages($images, $towerId)
    {
        foreach ($images as $image) {
            $path = $image->store('towers', 'public');

            TowerGallery::create([
                'tower_id' => $towerId,
                'path' => $path,
            ]);
        }
    }
}

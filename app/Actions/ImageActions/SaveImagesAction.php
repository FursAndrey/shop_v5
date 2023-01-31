<?php

namespace App\Actions\ImageActions;

use Generator;
use Illuminate\Http\UploadedFile;

class SaveImagesAction
{
    public static function one(UploadedFile $image): string
    {
        return $image->store('uploads', 'public');
    }
    
    public static function all(array $files): Generator
    {
        if (!is_null($files)) {
            foreach ($files as $image) {
                yield self::one($image);
            }
        }
    }
}
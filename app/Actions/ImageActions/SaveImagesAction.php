<?php

namespace App\Actions\ImageActions;

use Illuminate\Http\UploadedFile;

class SaveImagesAction
{
    public static function one(UploadedFile $image): string
    {
        return $image->store('uploads', 'public');
    }
    
    public static function all(array $files): array
    {
        $fileNames = [];
        if (!is_null($files)) {
            foreach ($files as $image) {
                $fileNames[] = self::one($image);
            }
        }
        return $fileNames;
    }
}
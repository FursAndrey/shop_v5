<?php

namespace App\Actions\ImageActions;

use App\Models\Image;
use App\Models\Sku;

class DeleteImagesAction
{
    public static function all(Sku $sku): void
    {
        if (!is_null($sku->images)) {
            foreach ($sku->images as $image) {
                self::one($image);
            }
        }
    }

    public static function one(Image $image): void
    {
        if (file_exists($image->file_for_delete)) {
            unlink($image->file_for_delete);
        }
        $image->delete();
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ImageActions\DeleteImagesAction;
use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Sku;

class ImageController extends Controller
{
    public function destroyAll(Sku $sku)
    {
        DeleteImagesAction::all($sku);
        return response()->noContent();
    }

    public function destroyOne(Image $image)
    {
        DeleteImagesAction::one($image);
        return response()->noContent();
    }
}

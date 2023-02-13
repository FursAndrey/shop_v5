<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ImageActions\DeleteImagesAction;
use App\Actions\ImageActions\SaveImagesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
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

    public function saveImageForSku(ImageRequest $request, Sku $sku)
    {
        SaveImagesAction::withDb($request->validated(), $sku->id);
        return response()->noContent();
    }
}

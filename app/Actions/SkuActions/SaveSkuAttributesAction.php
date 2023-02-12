<?php

namespace App\Actions\SkuActions;

use App\Actions\ImageActions\SaveImagesAction;
use App\Http\Requests\SkuRequest;
use App\Models\Image;
use App\Models\Sku;

class SaveSkuAttributesAction
{
    public function __invoke(SkuRequest $request, Sku $sku): void
    {
        $sku->options()->sync($request->option_id);

        if (!is_null($request->image)) {
            SaveImagesAction::withDb($request->image, $sku->id);
        }
    }
}
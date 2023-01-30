<?php

namespace App\Actions\SkuActions;

use App\Actions\ImageActions\DeleteImagesAction;
use App\Models\Sku;

class DeleteSkuAction
{
    public function __invoke(Sku $sku): void
    {
        $sku->options()->detach();
        DeleteImagesAction::all($sku);
        $sku->delete();
    }
}
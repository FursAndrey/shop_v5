<?php

namespace App\Actions;

use App\Models\Sku;

class CreateSkuAction
{
    public function __invoke(array $sku): Sku
    {
        return Sku::create($sku);
    }
}
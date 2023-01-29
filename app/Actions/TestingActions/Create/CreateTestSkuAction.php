<?php

namespace App\Actions\TestingActions\Create;

use App\Models\Sku;

class CreateTestSkuAction
{
    public function __invoke(array $sku): Sku
    {
        return Sku::create($sku);
    }
}
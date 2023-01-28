<?php

namespace App\Actions;

use App\Models\Product;

class CreatePrductAction
{
    public function __invoke(array $product): Product
    {
        return Product::create($product);
    }
}
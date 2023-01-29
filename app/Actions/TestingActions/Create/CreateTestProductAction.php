<?php

namespace App\Actions\TestingActions\Create;

use App\Models\Product;

class CreateTestProductAction
{
    public function __invoke(array $product): Product
    {
        return Product::create($product);
    }
}
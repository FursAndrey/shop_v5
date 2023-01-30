<?php

namespace App\Actions\ProductActions;

use App\Models\Product;

class DeleteProductAction
{
    public function __invoke(Product $product): void
    {
        $product->properties()->detach();
        $product->delete();
    }
}
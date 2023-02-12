<?php

namespace App\Actions\TestingActions\Get;

use Illuminate\Http\Testing\File;

class GetTestSkuWithImageAction
{
    public function __invoke(int $product_id, int $option_id, File $file): array
    {
        $sku = (new GetTestSkuWithoutImageAction)($product_id, $option_id);
        $sku['image'] = [
            $file
        ];
        return $sku;
    }
}
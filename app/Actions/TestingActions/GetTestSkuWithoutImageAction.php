<?php

namespace App\Actions\TestingActions;

class GetTestSkuWithoutImageAction
{
    public function __invoke(int $product_id, int $option_id): array
    {
        return [
            'price' => mt_rand(10, 100)/10,
            'count' => mt_rand(1, 100),
            'product_id' => $product_id,
            'option_id' => [
                $option_id,
            ],
        ];
    }
}
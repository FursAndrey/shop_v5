<?php

namespace App\Actions\TestingActions;

class GetTestProductAction
{
    public function __invoke(int $property_id, int $category_id): array
    {
        return [
            'name' => 'prod'.md5(microtime()),
            'description' => 'description',
            'category_id' => $category_id,
            'property_id' => [
                $property_id,
            ],
        ];
    }
}
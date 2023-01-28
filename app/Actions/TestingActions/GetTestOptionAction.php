<?php

namespace App\Actions\TestingActions;

class GetTestOptionAction
{
    public function __invoke(int $property_id): array
    {
        return [
            'name' => 'opt'.md5(microtime()),
            'property_id' => $property_id,
        ];
    }
}
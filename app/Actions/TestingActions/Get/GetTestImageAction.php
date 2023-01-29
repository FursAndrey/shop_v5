<?php

namespace App\Actions\TestingActions\Get;

class GetTestImageAction
{
    public function __invoke(int $sku_id, string $fileName): array
    {
        return [
            'sku_id' => $sku_id,
            'file' => $fileName
        ];
    }
}
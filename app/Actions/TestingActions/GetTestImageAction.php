<?php

namespace App\Actions\TestingActions;

use Illuminate\Http\Testing\File;

class GetTestImageAction
{
    public function __invoke(int $sku_id, File $file): array
    {
        return [
            'sku_id' => $sku_id,
            'file' => $file->hashName()
        ];
    }
}
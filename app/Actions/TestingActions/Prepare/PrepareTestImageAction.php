<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestImageAction;
use App\Actions\TestingActions\Get\GetTestImageAction;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Testing\File;

class PrepareTestImageAction
{
    public function short(int $skuId): array
    {
        $image = $this->intoDB($skuId);

        $arr = [
            'id' => $image->id,
        ];

        return $arr;
    }

    public function full(int $skuId): array
    {
        return $this->short($skuId);
    }

    public function noDB(): File
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');

        return $file;
    }

    private function intoDB(int $skuId): Image
    {
        $file = $this->noDB();
        return (new CreateTestImageAction)((new GetTestImageAction)($skuId, $file->hashName()));
    }
}
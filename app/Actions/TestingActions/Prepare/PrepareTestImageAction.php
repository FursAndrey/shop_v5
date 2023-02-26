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
    // public function short(): array
    // {
    //     $category = $this->intoDB();

    //     $arr = [
    //         'id' => $category->id,
    //         'name' => $category->name,
    //     ];

    //     return $arr;
    // }

    // public function full(): array
    // {
    //     $category = $this->intoDB();

    //     $arr = [
    //         'id' => $category->id,
    //         'name' => $category->name,
    //         'products' => [],
    //     ];

    //     return $arr;
    // }

    public function noDB(): File
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');

        return $file;
    }

    public function intoDB(int $skuId): Image
    {
        $file = $this->noDB();
        return (new CreateTestImageAction)((new GetTestImageAction)($skuId, $file->hashName()));
    }
}
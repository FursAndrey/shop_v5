<?php

namespace App\Actions\TestingActions\Prepare;

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

    // private function intoDB(): Category
    // {
    //     return (new CreateTestCategoryAction)(
    //         (new GetTestCategoryAction)()
    //     );
    // }
}
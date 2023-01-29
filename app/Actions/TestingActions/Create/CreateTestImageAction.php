<?php

namespace App\Actions\TestingActions\Create;

use App\Models\Image;

class CreateTestImageAction
{
    public function __invoke(array $image): Image
    {
        return Image::create($image);
    }
}
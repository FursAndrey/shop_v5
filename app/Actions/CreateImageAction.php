<?php

namespace App\Actions;

use App\Models\Image;

class CreateImageAction
{
    public function __invoke(array $image): Image
    {
        return Image::create($image);
    }
}
<?php

namespace App\Actions\TestingActions\Get;

class GetTestCategoryAction
{
    public function __invoke(): array
    {
        return [
            'name' => 'cat'.md5(microtime()),
        ];
    }
}
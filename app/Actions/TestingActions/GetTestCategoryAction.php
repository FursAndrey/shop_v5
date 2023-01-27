<?php

namespace App\Actions\TestingActions;

class GetTestCategoryAction
{
    public function __invoke(): array
    {
        return [
            'name' => 'cat'.md5(microtime()),
        ];
    }
}
<?php

namespace App\Actions\TestingActions\Get;

class GetTestPropertyAction
{
    public function __invoke(): array
    {
        return [
            'name' => 'prop'.md5(microtime()),
        ];
    }
}
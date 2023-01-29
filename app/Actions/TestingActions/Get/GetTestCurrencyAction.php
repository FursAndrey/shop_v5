<?php

namespace App\Actions\TestingActions\Get;

class GetTestCurrencyAction
{
    public function __invoke(): array
    {
        return [
            'code' => 'cat',
            'rate' => mt_rand(10, 100)/10,
        ];
    }
}
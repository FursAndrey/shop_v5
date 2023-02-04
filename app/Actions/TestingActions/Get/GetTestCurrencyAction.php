<?php

namespace App\Actions\TestingActions\Get;

class GetTestCurrencyAction
{
    public function __invoke(): array
    {
        return [
            'code' => substr(md5(microtime()), 0, 3),
            'rate' => mt_rand(10, 100)/10,
        ];
    }
}
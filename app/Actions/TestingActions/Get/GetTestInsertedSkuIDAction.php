<?php

namespace App\Actions\TestingActions\Get;

use Illuminate\Testing\TestResponse;

class GetTestInsertedSkuIDAction
{
    public function __invoke(TestResponse $response): int
    {
        return $response->original[0]->id;
    }
}
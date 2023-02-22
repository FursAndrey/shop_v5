<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestPropertyAction;
use App\Actions\TestingActions\Get\GetTestPropertyAction;

class PrepareTestPropertyAction
{
    public function prepare(bool $isFull = false): array
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );

        $arr = [
            'id' => $property->id,
            'name' => $property->name,
        ];

        if ($isFull) {
            $arr['products'] = [];
            $arr['options'] = [];
        }

        return $arr;
    }
}
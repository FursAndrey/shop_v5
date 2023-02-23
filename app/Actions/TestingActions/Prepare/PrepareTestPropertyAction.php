<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestPropertyAction;
use App\Actions\TestingActions\Get\GetTestPropertyAction;
use App\Models\Property;

class PrepareTestPropertyAction
{
    public function short(): array
    {
        $property = $this->intoDB();

        $arr = [
            'id' => $property->id,
            'name' => $property->name,
        ];

        return $arr;
    }

    public function full(): array
    {
        $property = $this->intoDB();

        $arr = [
            'id' => $property->id,
            'name' => $property->name,
            'products' => [],
            'options' => [],
        ];

        return $arr;
    }

    public function noDB(): array
    {
        return (new GetTestPropertyAction)();
    }

    private function intoDB(): Property
    {
        return (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
    }
}
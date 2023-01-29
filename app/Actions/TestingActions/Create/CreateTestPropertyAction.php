<?php

namespace App\Actions\TestingActions\Create;

use App\Models\Property;

class CreateTestPropertyAction
{
    public function __invoke(array $property): Property
    {
        return Property::create($property);
    }
}
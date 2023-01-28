<?php

namespace App\Actions;

use App\Models\Property;

class CreatePropertyAction
{
    public function __invoke(array $property): Property
    {
        return Property::create($property);
    }
}
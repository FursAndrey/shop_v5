<?php

namespace App\Actions;

use App\Models\Option;

class CreateOptionAction
{
    public function __invoke(array $option): Option
    {
        return Option::create($option);
    }
}
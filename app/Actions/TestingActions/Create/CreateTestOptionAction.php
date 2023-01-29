<?php

namespace App\Actions\TestingActions\Create;

use App\Models\Option;

class CreateTestOptionAction
{
    public function __invoke(array $option): Option
    {
        return Option::create($option);
    }
}
<?php

namespace App\Actions\TestingActions\Create;

use App\Models\Currency;

class CreateTestCurrencyAction
{
    public function __invoke(array $currency): Currency
    {
        return Currency::create($currency);
    }
}
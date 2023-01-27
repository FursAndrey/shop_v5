<?php

namespace App\Actions;

use App\Models\Currency;

class CreateCurrencyAction
{
    public function __invoke(array $currency): Currency
    {
        return Currency::create($currency);
    }
}
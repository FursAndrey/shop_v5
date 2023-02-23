<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestCurrencyAction;
use App\Actions\TestingActions\Get\GetTestCurrencyAction;
use App\Models\Currency;

class PrepareTestCurrencyAction
{
    public function short(): array
    {
        $currency = $this->getProperty();

        $arr = [
            'id' => $currency->id,
            'code' => $currency->code,
            'rate' => $currency->rate,
        ];

        return $arr;
    }

    public function full(): array
    {
        // $currency = $this->getProperty();

        // $arr = [
        //     'id' => $currency->id,
        //     'code' => $currency->code,
        //     'rate' => $currency->rate,
        // ];

        // return $arr;
        return $this->short();
    }

    public function noDB(): array
    {
        return (new GetTestCurrencyAction)();
    }

    private function getProperty(): Currency
    {
        return (new CreateTestCurrencyAction)(
            (new GetTestCurrencyAction)()
        );
    }
}
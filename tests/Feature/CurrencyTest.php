<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestCurrencyAction;
use App\Actions\TestingActions\Get\GetTestCurrencyAction;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_status_200()
    {
        $response = $this->get('/api/currencies');

        $response->assertStatus(200);
    }

    public function test_index_page_json_without_data()
    {
        $response = $this->get('/api/currencies');

        $response->assertJsonPath('data', []);
    }

    public function test_index_page_json_with_data()
    {
        $currency = (new CreateTestCurrencyAction)(
            (new GetTestCurrencyAction)()
        );

        $response = $this->get('/api/currencies');

        $response->assertJsonFragment(
            [
                'id' => $currency->id,
                'code' => $currency->code,
                'rate' => $currency->rate,
            ]
        );
    }

    public function test_currency_all_page_json_with_data()
    {
        $currency1 = (new CreateTestCurrencyAction)(
            (new GetTestCurrencyAction)()
        );
        $currency2 = (new CreateTestCurrencyAction)(
            (new GetTestCurrencyAction)()
        );

        $response = $this->get('/api/currency/all');

        $response->assertExactJson(
            [
                [
                    'id' => $currency1->id,
                    'code' => $currency1->code,
                    'rate' => $currency1->rate,
                ],
                [
                    'id' => $currency2->id,
                    'code' => $currency2->code,
                    'rate' => $currency2->rate,
                ]
            ]
        );
    }

    public function test_show_page_status_200()
    {
        $currency = (new CreateTestCurrencyAction)(
            (new GetTestCurrencyAction)()
        );

        $response = $this->get('/api/currencies/'.$currency->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $currency = (new CreateTestCurrencyAction)(
            (new GetTestCurrencyAction)()
        );

        $response = $this->get('/api/currencies/'.$currency->id);

        $response->assertJsonFragment(
            [
                'id' => $currency->id,
                'code' => $currency->code,
                'rate' => $currency->rate,
            ]
        );
    }

    public function test_store()
    {
        $currency = (new GetTestCurrencyAction)();
        $this->assertDatabaseCount('currencies', 0);
        $this->post('/api/currencies', $currency);

        $this->assertDatabaseCount('currencies', 1);
        $this->assertDatabaseHas('currencies', $currency);
    }

    public function test_destroy()
    {
        $currency = (new CreateTestCurrencyAction)(
            (new GetTestCurrencyAction)()
        );

        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);
        $this->delete('/api/currencies/'.$currency->id);
        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }

    public function test_update()
    {
        $oldCurrency = (new GetTestCurrencyAction)();
        $currency = (new CreateTestCurrencyAction)($oldCurrency);
        $this->assertDatabaseHas('currencies', $oldCurrency);

        $newCurrency = (new GetTestCurrencyAction)();
        $this->put('/api/currencies/'.$currency->id, $newCurrency);

        $this->assertDatabaseMissing('currencies', $oldCurrency);
        $this->assertDatabaseHas('currencies', $newCurrency);
    }
}

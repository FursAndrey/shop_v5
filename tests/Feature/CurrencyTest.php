<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Prepare\PrepareTestCurrencyAction;
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
        $currency = (new PrepareTestCurrencyAction)->full();

        $response = $this->get('/api/currencies');

        $response->assertJsonFragment($currency);
    }

    public function test_currency_all_page_json_with_data()
    {
        $currency1 = (new PrepareTestCurrencyAction)->short();
        $currency2 = (new PrepareTestCurrencyAction)->short();

        $response = $this->get('/api/currency/all');

        $response->assertExactJson(
            [
                $currency1,
                $currency2,
            ]
        );
    }

    public function test_show_page_status_200()
    {
        $currency = (new PrepareTestCurrencyAction)->short();

        $response = $this->get('/api/currencies/'.$currency['id']);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $currency = (new PrepareTestCurrencyAction)->full();

        $response = $this->get('/api/currencies/'.$currency['id']);

        $response->assertJsonFragment($currency);
    }

    public function test_store()
    {
        $currency = (new PrepareTestCurrencyAction)->noDB();
        $this->assertDatabaseCount('currencies', 0);
        $this->post('/api/currencies', $currency);

        $this->assertDatabaseCount('currencies', 1);
        $this->assertDatabaseHas('currencies', $currency);
    }

    public function test_destroy()
    {
        $currency = (new PrepareTestCurrencyAction)->short();

        $this->assertDatabaseHas('currencies', $currency);
        $this->delete('/api/currencies/'.$currency['id']);
        $this->assertDatabaseMissing('currencies', $currency);
    }

    public function test_update()
    {
        $oldCurrency = (new PrepareTestCurrencyAction)->short();
        $this->assertDatabaseHas('currencies', $oldCurrency);

        $newCurrency = (new PrepareTestCurrencyAction)->noDB();
        $this->put('/api/currencies/'.$oldCurrency['id'], $newCurrency);

        $this->assertDatabaseMissing('currencies', $oldCurrency);
        $this->assertDatabaseHas('currencies', $newCurrency);
    }
}

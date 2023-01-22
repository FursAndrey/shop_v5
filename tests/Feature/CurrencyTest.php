<?php

namespace Tests\Feature;

use App\Models\Currency;
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
        $currency = Currency::create(
            [
                'code' => 'cat',
                'rate' => 15.5,
            ]
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
    
    public function test_show_page_status_200()
    {
        $currency = Currency::create(
            [
                'code' => 'cat',
                'rate' => 15.5,
            ]
        );

        $response = $this->get('/api/currencies/'.$currency->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $currency = Currency::create(
            [
                'code' => 'cat',
                'rate' => 15.5,
            ]
        );

        $response = $this->get('/api/currencies/'.$currency->id);

        $response->assertJsonPath('code', $currency->code);
    }

    public function test_store()
    {
        $currency = [
            'code' => 'cat',
            'rate' => 15.5,
        ];
        $this->assertDatabaseCount('currencies', 0);
        $response = $this->post('/api/currencies', $currency);

        $this->assertDatabaseCount('currencies', 1);
        $this->assertDatabaseHas('currencies', $currency);
    }

    public function test_destroy()
    {
        $currency = Currency::create(
            [
                'code' => 'cat',
                'rate' => 15.5,
            ]
        );
        
        $this->assertDatabaseHas('currencies', ['id' => $currency->id]);
        $this->delete('/api/currencies/'.$currency->id);
        $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
    }

    public function test_update()
    {
        $oldCurrency = [
            'code' => 'cat',
            'rate' => 15.5,
        ];
        $currency = Currency::create($oldCurrency);
        $this->assertDatabaseHas('currencies', ['code' => $oldCurrency['code']]);

        $newCurrency = [
            'code' => 'QWE',
            'rate' => 15.5,
        ];
        $this->put('/api/currencies/'.$currency->id, $newCurrency);
        
        $this->assertDatabaseMissing('currencies', $oldCurrency);
        $this->assertDatabaseHas('currencies', $newCurrency);
    }
}

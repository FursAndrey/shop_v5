<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Prepare\PrepareTestOptionAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_status_200()
    {
        $response = $this->get('/api/options');

        $response->assertStatus(200);
    }

    public function test_index_page_json_without_data()
    {
        $response = $this->get('/api/options');

        $response->assertJsonPath('data', []);
    }

    public function test_index_page_json_with_data()
    {
        $prepareOption = new PrepareTestOptionAction;
        $expectedOption = $prepareOption->prepare(isFull: true);
        
        $response = $this->get('/api/options');

        $response->assertJsonFragment($expectedOption);
    }

    public function test_option_all_page_json_with_data()
    {
        $prepareOption = new PrepareTestOptionAction;
        $expectedOption = $prepareOption->prepare(isFull: false, optionsCount: 2);

        $response = $this->get('/api/option/all');

        $response->assertExactJson($expectedOption);
    }

    public function test_show_page_status_200()
    {
        $prepareOption = new PrepareTestOptionAction;
        $expectedOption = $prepareOption->prepare(isFull: false);

        $response = $this->get('/api/options/'.$expectedOption['id']);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $prepareOption = new PrepareTestOptionAction;
        $expectedOption = $prepareOption->prepare(isFull: true);

        $response = $this->get('/api/options/'.$expectedOption['id']);

        $response->assertJsonFragment($expectedOption);
    }

    public function test_store()
    {
        $prepareOption = new PrepareTestOptionAction;
        $expectedOption = $prepareOption->prepareNoCreate();

        $this->assertDatabaseCount('options', 0);
        $this->post('/api/options', $expectedOption);

        $this->assertDatabaseCount('options', 1);
        $this->assertDatabaseHas('options', $expectedOption);
    }

    public function test_destroy()
    {
        $prepareOption = new PrepareTestOptionAction;
        $expectedOption = $prepareOption->prepare(isFull: false);

        $this->assertDatabaseHas('options', $expectedOption);
        $this->delete('/api/options/'.$expectedOption['id']);
        $this->assertDatabaseMissing('options', $expectedOption);
    }

    public function test_update()
    {
        $prepareOption = new PrepareTestOptionAction;
        $expectedOldOption = $prepareOption->prepare(isFull: false);

        $this->assertDatabaseHas('options', $expectedOldOption);

        $expectedNewOption = $prepareOption->prepareNoCreate();
        $this->put('/api/options/'.$expectedOldOption['id'], $expectedNewOption);

        $this->assertDatabaseMissing('options', $expectedOldOption);
        $this->assertDatabaseHas('options', $expectedNewOption);
    }
}

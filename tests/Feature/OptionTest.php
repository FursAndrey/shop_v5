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
        $option = (new PrepareTestOptionAction)->full();

        $response = $this->get('/api/options');

        $response->assertJsonFragment($option);
    }

    public function test_option_all_page_json_with_data()
    {
        $option1 = (new PrepareTestOptionAction)->short();
        $option2 = (new PrepareTestOptionAction)->short();

        $response = $this->get('/api/option/all');

        $response->assertExactJson(
            [
                $option1,
                $option2,
            ]
        );
    }

    public function test_show_page_status_200()
    {
        $option = (new PrepareTestOptionAction)->short();

        $response = $this->get('/api/options/'.$option['id']);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $option = (new PrepareTestOptionAction)->full();

        $response = $this->get('/api/options/'.$option['id']);

        $response->assertJsonFragment($option);
    }

    public function test_store()
    {
        $option = (new PrepareTestOptionAction)->noDB();
        $this->assertDatabaseCount('options', 0);
        $this->post('/api/options', $option);

        $this->assertDatabaseCount('options', 1);
        $this->assertDatabaseHas('options', $option);
    }

    public function test_destroy()
    {
        $option = (new PrepareTestOptionAction)->short();

        $this->assertDatabaseHas('options', $option);
        $this->delete('/api/options/'.$option['id']);
        $this->assertDatabaseMissing('options', $option);
    }

    public function test_update()
    {
        $oldOption = (new PrepareTestOptionAction)->short();

        $this->assertDatabaseHas('options', $oldOption);

        $newOption = (new PrepareTestOptionAction)->noDB();
        $this->put('/api/options/'.$oldOption['id'], $newOption);

        $this->assertDatabaseMissing('options', $oldOption);
        $this->assertDatabaseHas('options', $newOption);
    }
}
<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Get\GetTestPropertyAction;
use App\Actions\TestingActions\Prepare\PrepareTestPropertyAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_status_200()
    {
        $response = $this->get('/api/properties');

        $response->assertStatus(200);
    }

    public function test_index_page_json_without_data()
    {
        $response = $this->get('/api/properties');

        $response->assertJsonPath('data', []);
    }

    public function test_index_page_json_with_data()
    {
        $expectedProperty = (new PrepareTestPropertyAction)(isFull: false);

        $response = $this->get('/api/properties');

        $response->assertJsonFragment($expectedProperty);
    }

    public function test_property_all_page_json_with_data()
    {
        $expectedProperties[] = (new PrepareTestPropertyAction)(isFull: false);
        $expectedProperties[] = (new PrepareTestPropertyAction)(isFull: false);

        $response = $this->get('/api/property/all');

        $response->assertExactJson($expectedProperties);
    }

    public function test_show_page_status_200()
    {
        $expectedProperty = (new PrepareTestPropertyAction)(isFull: false);

        $response = $this->get('/api/properties/'.$expectedProperty['id']);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $expectedProperty = (new PrepareTestPropertyAction)(isFull: true);

        $response = $this->get('/api/properties/'.$expectedProperty['id']);

        $response->assertExactJson($expectedProperty);
    }

    public function test_store()
    {
        $property = (new GetTestPropertyAction)();
        $this->assertDatabaseCount('properties', 0);
        $this->post('/api/properties', $property);

        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseHas('properties', $property);
    }

    public function test_destroy()
    {
        $expectedProperty = (new PrepareTestPropertyAction)(isFull: false);

        $this->assertDatabaseHas('properties', $expectedProperty);
        $this->delete('/api/properties/'.$expectedProperty['id']);
        $this->assertDatabaseMissing('properties', $expectedProperty);
    }

    public function test_update()
    {
        $expectedProperty = (new PrepareTestPropertyAction)(isFull: false);
        $this->assertDatabaseHas('properties', $expectedProperty);

        $newProperty = (new GetTestPropertyAction)();
        $this->put('/api/properties/'.$expectedProperty['id'], $newProperty);

        $this->assertDatabaseMissing('properties', $expectedProperty);
        $this->assertDatabaseHas('properties', $newProperty);
    }
}

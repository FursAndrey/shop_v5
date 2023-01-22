<?php

namespace Tests\Feature;

use App\Models\Property;
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
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );

        $response = $this->get('/api/properties');
        
        $response->assertJsonFragment(
            [
                'id' => $property->id,
                'name' => $property->name,
                'products' => [],
                'options' => [],
            ]
        );
    }
    
    public function test_show_page_status_200()
    {
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );

        $response = $this->get('/api/properties/'.$property->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );

        $response = $this->get('/api/properties/'.$property->id);

        $response->assertJsonPath('name', $property->name);
    }

    public function test_store()
    {
        $property = [
            'name' => 'prop',
        ];
        $this->assertDatabaseCount('properties', 0);
        $response = $this->post('/api/properties', $property);

        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseHas('properties', $property);
    }

    public function test_destroy()
    {
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );
        
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
        $this->delete('/api/properties/'.$property->id);
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_update()
    {
        $oldProperty = [
            'name' => 'prop',
        ];
        $property = Property::create($oldProperty);
        $this->assertDatabaseHas('properties', ['name' => $oldProperty['name']]);

        $newProperty = [
            'name' => 'new prop',
        ];
        $this->put('/api/properties/'.$property->id, $newProperty);
        
        $this->assertDatabaseMissing('properties', $oldProperty);
        $this->assertDatabaseHas('properties', $newProperty);
    }
}

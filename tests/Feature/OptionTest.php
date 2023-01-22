<?php

namespace Tests\Feature;

use App\Models\Option;
use App\Models\Property;
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
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );

        $response = $this->get('/api/options');
        
        $response->assertJsonFragment(
            [
                'id' => $option->id,
                'name' => $option->name,
                'property' => [
                    'id' => $property->id,
                    'name' => $property->name,
                ],
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
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );

        $response = $this->get('/api/options/'.$option->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );

        $response = $this->get('/api/options/'.$option->id);

        $response->assertJsonPath('name', $option->name);
    }

    public function test_store()
    {
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );

        $option = [
            'name' => 'opti',
            'property_id' => $property->id,
        ];
        $this->assertDatabaseCount('options', 0);
        $response = $this->post('/api/options', $option);

        $this->assertDatabaseCount('options', 1);
        $this->assertDatabaseHas('options', $option);
    }

    public function test_destroy()
    {
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        
        $this->assertDatabaseHas('options', ['id' => $option->id]);
        $this->delete('/api/options/'.$option->id);
        $this->assertDatabaseMissing('options', ['id' => $option->id]);
    }

    public function test_update()
    {
        $property = Property::create(
            [
                'name' => 'prop',
            ]
        );

        $oldOption = [
            'name' => 'opti',
            'property_id' => $property->id,
        ];
        $option = Option::create($oldOption);
        $this->assertDatabaseHas('options', ['name' => $oldOption['name']]);

        $newOption = [
            'name' => 'new opti',
            'property_id' => $property->id,
        ];
        $this->put('/api/options/'.$option->id, $newOption);
        
        $this->assertDatabaseMissing('options', $oldOption);
        $this->assertDatabaseHas('options', $newOption);
    }
}

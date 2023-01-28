<?php

namespace Tests\Feature;

use App\Actions\CreateOptionAction;
use App\Actions\CreatePropertyAction;
use App\Actions\TestingActions\GetTestOptionAction;
use App\Actions\TestingActions\GetTestPropertyAction;

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
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
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
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );

        $response = $this->get('/api/options/'.$option->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );

        $response = $this->get('/api/options/'.$option->id);

        $response->assertJsonPath('name', $option->name);
    }

    public function test_store()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );

        $option = (new GetTestOptionAction)($property->id);
        $this->assertDatabaseCount('options', 0);
        $this->post('/api/options', $option);

        $this->assertDatabaseCount('options', 1);
        $this->assertDatabaseHas('options', $option);
    }

    public function test_destroy()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        
        $this->assertDatabaseHas('options', ['id' => $option->id]);
        $this->delete('/api/options/'.$option->id);
        $this->assertDatabaseMissing('options', ['id' => $option->id]);
    }

    public function test_update()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );

        $oldOption = (new GetTestOptionAction)($property->id);
        $option = (new CreateOptionAction)($oldOption);
        $this->assertDatabaseHas('options', $oldOption);

        $newOption = (new GetTestOptionAction)($property->id);
        $this->put('/api/options/'.$option->id, $newOption);
        
        $this->assertDatabaseMissing('options', $oldOption);
        $this->assertDatabaseHas('options', $newOption);
    }
}

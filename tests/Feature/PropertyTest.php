<?php
namespace Tests\Feature;
use App\Actions\TestingActions\Create\CreateTestPropertyAction;
use App\Actions\TestingActions\Get\GetTestPropertyAction;
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
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
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
    public function test_property_all_page_json_with_data()
    {
        $property1 = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $property2 = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $response = $this->get('/api/property/all');
        $response->assertExactJson(
            [
                [
                    'id' => $property1->id,
                    'name' => $property1->name,
                ],
                [
                    'id' => $property2->id,
                    'name' => $property2->name,
                ]
            ]
        );
    }
    public function test_show_page_status_200()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $response = $this->get('/api/properties/'.$property->id);
        $response->assertStatus(200);
    }
    public function test_show_page_json_data()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );

        $response = $this->get('/api/properties/'.$property->id);

        $response->assertExactJson(
            [
                'id' => $property->id,
                'name' => $property->name,
                'products' => [],
                'options' => [],
            ]
        );
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
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $this->assertDatabaseHas('properties', ['id' => $property->id]);
        $this->delete('/api/properties/'.$property->id);
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }
    public function test_update()
    {
        $oldProperty = (new GetTestPropertyAction)();
        $property = (new CreateTestPropertyAction)($oldProperty);
        $this->assertDatabaseHas('properties', $oldProperty);
        $newProperty = (new GetTestPropertyAction)();
        $this->put('/api/properties/'.$property->id, $newProperty);
        $this->assertDatabaseMissing('properties', $oldProperty);
        $this->assertDatabaseHas('properties', $newProperty);
    }
}
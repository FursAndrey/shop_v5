<?php
namespace Tests\Feature;
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
        $property = (new PrepareTestPropertyAction)->full();

        $response = $this->get('/api/properties');
        $response->assertJsonFragment($property);
    }
    public function test_property_all_page_json_with_data()
    {
        $property1 = (new PrepareTestPropertyAction)->short();
        $property2 = (new PrepareTestPropertyAction)->short();

        $response = $this->get('/api/property/all');
        $response->assertExactJson(
            [
                $property1,
                $property2,
            ]
        );
    }
    public function test_show_page_status_200()
    {
        $property = (new PrepareTestPropertyAction)->short();

        $response = $this->get('/api/properties/'.$property['id']);
        $response->assertStatus(200);
    }
    public function test_show_page_json_data()
    {
        $property = (new PrepareTestPropertyAction)->full();

        $response = $this->get('/api/properties/'.$property['id']);

        $response->assertExactJson($property);
    }

    public function test_store()
    {
        $property = (new PrepareTestPropertyAction)->noDB();

        $this->assertDatabaseCount('properties', 0);
        $this->post('/api/properties', $property);
        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseHas('properties', $property);
    }
    public function test_destroy()
    {
        $property = (new PrepareTestPropertyAction)->short();

        $this->assertDatabaseHas('properties', $property);
        $this->delete('/api/properties/'.$property['id']);
        $this->assertDatabaseMissing('properties', $property);
    }
    public function test_update()
    {
        $oldProperty = (new PrepareTestPropertyAction)->short();

        $this->assertDatabaseHas('properties', $oldProperty);

        $newProperty = (new PrepareTestPropertyAction)->noDB();

        $this->put('/api/properties/'.$oldProperty['id'], $newProperty);
        $this->assertDatabaseMissing('properties', $oldProperty);
        $this->assertDatabaseHas('properties', $newProperty);
    }
}
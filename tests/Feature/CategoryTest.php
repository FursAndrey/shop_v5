<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestProductAction;
use App\Actions\TestingActions\Create\CreateTestPropertyAction;
use App\Actions\TestingActions\Get\GetTestProductAction;
use App\Actions\TestingActions\Get\GetTestPropertyAction;
use App\Actions\TestingActions\Prepare\PrepareTestCategoryAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_status_200()
    {
        $response = $this->get('/api/categories');

        $response->assertStatus(200);
    }

    public function test_index_page_json_without_data()
    {
        $response = $this->get('/api/categories');

        $response->assertJsonPath('data', []);
    }

    public function test_category_all_page_json_with_data()
    {
        $category1 = (new PrepareTestCategoryAction)->short();
        $category2 = (new PrepareTestCategoryAction)->short();

        $response = $this->get('/api/category/all');

        $response->assertExactJson(
            [
                $category1,
                $category2,
            ]
        );
    }

    public function test_index_page_json_with_data()
    {
        $category = (new PrepareTestCategoryAction)->full();

        $response = $this->get('/api/categories');

        $response->assertJsonFragment($category);
    }

    public function test_show_page_status_200()
    {
        $category = (new PrepareTestCategoryAction)->short();

        $response = $this->get('/api/categories/'.$category['id']);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $category = (new PrepareTestCategoryAction)->full();

        $response = $this->get('/api/categories/'.$category['id']);

        $response->assertJsonFragment($category);
    }

    public function test_store()
    {
        $category = (new PrepareTestCategoryAction)->noDB();
        $this->assertDatabaseCount('categories', 0);
        $this->post('/api/categories', $category);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', $category);
    }

    public function test_destroy_without_product()
    {
        $category = (new PrepareTestCategoryAction)->short();

        $this->assertDatabaseHas('categories', $category);
        $this->delete('/api/categories/'.$category['id']);
        $this->assertDatabaseMissing('categories', $category);
    }

    public function test_destroy_with_product()
    {
        $category = (new PrepareTestCategoryAction)->short();

        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        
        (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category['id'])
        );
        
        $this->assertDatabaseHas('categories', $category);
        $response = $this->delete('/api/categories/'.$category['id']);
        $response->assertStatus(409);
        $this->assertDatabaseHas('categories', $category);
    }

    public function test_update()
    {
        $oldCategory = (new PrepareTestCategoryAction)->short();
        $this->assertDatabaseHas('categories', $oldCategory);

        $newCategory = (new PrepareTestCategoryAction)->noDB();
        $this->put('/api/categories/'.$oldCategory['id'], $newCategory);

        $this->assertDatabaseMissing('categories', $oldCategory);
        $this->assertDatabaseHas('categories', $newCategory);
    }
}

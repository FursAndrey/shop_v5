<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestCategoryAction;
use App\Actions\TestingActions\Get\GetTestCategoryAction;

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

    public function test_index_page_json_with_data()
    {
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );

        $response = $this->get('/api/categories');

        $response->assertJsonFragment(
            [
                'id' => $category->id,
                'name' => $category->name,
                'products' => [],
            ]
        );
    }

    public function test_show_page_status_200()
    {
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );

        $response = $this->get('/api/categories/'.$category->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );

        $response = $this->get('/api/categories/'.$category->id);

        $response->assertJsonPath('name', $category->name);
    }

    public function test_store()
    {
        $category = (new GetTestCategoryAction)();
        $this->assertDatabaseCount('categories', 0);
        $response = $this->post('/api/categories', $category);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', $category);
    }

    public function test_destroy()
    {
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->delete('/api/categories/'.$category->id);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_update()
    {
        $oldCategory = (new GetTestCategoryAction)();
        $category = (new CreateTestCategoryAction)($oldCategory);
        $this->assertDatabaseHas('categories', $oldCategory);

        $newCategory = (new GetTestCategoryAction)();
        $this->put('/api/categories/'.$category->id, $newCategory);

        $this->assertDatabaseMissing('categories', $oldCategory);
        $this->assertDatabaseHas('categories', $newCategory);
    }
}

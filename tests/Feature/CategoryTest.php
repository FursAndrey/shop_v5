<?php

namespace Tests\Feature;

use App\Models\Category;
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
        $category = Category::create(
            [
                'name' => 'cat',
            ]
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
        $category = Category::create(
            [
                'name' => 'cat',
            ]
        );

        $response = $this->get('/api/categories/'.$category->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $category = Category::create(
            [
                'name' => 'cat',
            ]
        );

        $response = $this->get('/api/categories/'.$category->id);

        $response->assertJsonPath('name', $category->name);
    }

    public function test_store()
    {
        $category = [
            'name' => 'cat',
        ];
        $this->assertDatabaseCount('categories', 0);
        $response = $this->post('/api/categories', $category);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', $category);
    }

    public function test_destroy()
    {
        $category = Category::create(
            [
                'name' => 'cat',
            ]
        );
        
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->delete('/api/categories/'.$category->id);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_update()
    {
        $oldCategory = [
            'name' => 'cat',
        ];
        $category = Category::create($oldCategory);
        $this->assertDatabaseHas('categories', ['name' => 'cat']);

        $newCategory = [
            'name' => 'new cat',
        ];
        $this->put('/api/categories/'.$category->id, $newCategory);
        
        $this->assertDatabaseMissing('categories', $oldCategory);
        $this->assertDatabaseHas('categories', $newCategory);
    }
}

<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestCategoryAction;
use App\Actions\TestingActions\Create\CreateTestProductAction;
use App\Actions\TestingActions\Create\CreateTestPropertyAction;
use App\Actions\TestingActions\Create\CreateTestProductPropertyRelationAction;

use App\Actions\TestingActions\Get\GetTestCategoryAction;
use App\Actions\TestingActions\Get\GetTestProductAction;
use App\Actions\TestingActions\Get\GetTestPropertyAction;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_status_200()
    {
        $response = $this->get('/api/products');

        $response->assertStatus(200);
    }

    public function test_index_page_json_without_data()
    {
        $response = $this->get('/api/products');

        $response->assertJsonPath('data', []);
    }

    public function test_index_page_json_with_data()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        (new CreateTestProductPropertyRelationAction)($property->id, $product->id);

        $response = $this->get('/api/products');
        
        $response->assertJsonFragment(
            [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
                'properties' => [
                    [
                        'id' => $property->id,
                        'name' => $property->name,
                    ]
                ],
            ]
        );
    }
    
    public function test_show_page_status_200()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );

        $response = $this->get('/api/products/'.$product->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );

        $response = $this->get('/api/products/'.$product->id);

        $response->assertJsonPath('name', $product->name);
    }

    public function test_store()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new GetTestProductAction)($property->id, $category->id);

        $this->assertDatabaseCount('products', 0);
        $this->post('/api/products', $product);

        $this->assertDatabaseCount('products', 1);

        unset($product['property_id']);

        $this->assertDatabaseHas('products', $product);
        $this->assertDatabaseHas(
            'product_property', 
            [
                'property_id' => $property->id,
            ]
        );
    }

    public function test_destroy()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        (new CreateTestProductPropertyRelationAction)($property->id, $product->id);
        
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->delete('/api/products/'.$product->id);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_update()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        
        $oldProduct = (new GetTestProductAction)($property->id, $category->id);
        $product = (new CreateTestProductAction)($oldProduct);
        
        (new CreateTestProductPropertyRelationAction)($property->id, $product->id);

        $this->assertDatabaseHas('products', ['name' => $oldProduct['name']]);

        $newProduct = (new GetTestProductAction)($property->id, $category->id);
        $this->put('/api/products/'.$product->id, $newProduct);
        
        unset($oldProduct['property_id']);
        unset($newProduct['property_id']);

        $this->assertDatabaseMissing('products', $oldProduct);
        $this->assertDatabaseHas('products', $newProduct);
        $this->assertDatabaseHas(
            'product_property', 
            [
                'product_id' => $product->id,
                'property_id' => $property->id,
            ]
        );
    }
}

<?php

namespace Tests\Feature;

use App\Actions\CreateCategoryAction;
use App\Actions\CreatePrductAction;
use App\Actions\CreatePropertyAction;
use App\Actions\TestingActions\GetTestCategoryAction;
use App\Actions\TestingActions\GetTestProductAction;
use App\Actions\TestingActions\GetTestPropertyAction;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        DB::table('product_property')->insert(
            [
                ['property_id'=>$property->id, 'product_id'=>$product->id],
            ]
        );

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
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );

        $response = $this->get('/api/products/'.$product->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );

        $response = $this->get('/api/products/'.$product->id);

        $response->assertJsonPath('name', $product->name);
    }

    public function test_store()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateCategoryAction)(
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
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        DB::table('product_property')->insert(
            [
                ['property_id'=>$property->id, 'product_id'=>$product->id],
            ]
        );
        
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->delete('/api/products/'.$product->id);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_update()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        
        $oldProduct = (new GetTestProductAction)($property->id, $category->id);
        $product = (new CreatePrductAction)($oldProduct);
        
        DB::table('product_property')->insert(
            [
                ['property_id'=>$property->id, 'product_id'=>$product->id],
            ]
        );

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

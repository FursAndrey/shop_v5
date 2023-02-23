<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestOptionAction;
use App\Actions\TestingActions\Create\CreateTestSkuAction;
use App\Actions\TestingActions\Get\GetTestOptionAction;
use App\Actions\TestingActions\Get\GetTestSkuWithoutImageAction;
use App\Actions\TestingActions\Prepare\PrepareTestProductAction;
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
        $product = (new PrepareTestProductAction)->full();

        $response = $this->get('/api/products');

        $response->assertJsonFragment($product);
    }

    public function test_product_all_page_json_with_data()
    {
        $product1 = (new PrepareTestProductAction)->short();
        $product2 = (new PrepareTestProductAction)->short();

        $response = $this->get('/api/product/all');

        $response->assertExactJson(
            [
                $product1,
                $product2,
            ]
        );
    }

    public function test_show_page_status_200()
    {
        $product = (new PrepareTestProductAction)->short();

        $response = $this->get('/api/products/'.$product['id']);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $product = (new PrepareTestProductAction)->full();

        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($product['properties'][0]['id'])
        );
        $sku = (new CreateTestSkuAction)(
            (new GetTestSkuWithoutImageAction)($product['id'], $option->id)
        );

        $response = $this->get('/api/products/'.$product['id']);

        $product['skus'] = [
            [
                'id' => $sku->id,
                'count' => $sku->count,
                'price' => $sku->price,
                'options' => []
            ]
        ];
        $response->assertJsonFragment($product);
    }

    public function test_store()
    {
        $product = (new PrepareTestProductAction)->noDB();

        $this->assertDatabaseCount('products', 0);
        $this->post('/api/products', $product);

        $this->assertDatabaseCount('products', 1);

        unset($product['property_id']);

        $this->assertDatabaseHas('products', $product);
        //продумать необходимость этой проверки
        // $this->assertDatabaseHas(
        //     'product_property',
        //     [
        //         'property_id' => $property->id,
        //     ]
        // );
    }

    public function test_destroy()
    {
        $product = (new PrepareTestProductAction)->short();

        $this->assertDatabaseHas('products', $product);
        $this->delete('/api/products/'.$product['id']);
        $this->assertDatabaseMissing('products', $product);
    }

    public function test_update()
    {
        $oldProduct = (new PrepareTestProductAction)->short();

        $this->assertDatabaseHas('products', ['name' => $oldProduct['name']]);

        $newProduct = (new PrepareTestProductAction)->noDB();
        $this->put('/api/products/'.$oldProduct['id'], $newProduct);

        unset($oldProduct['property_id']);
        unset($newProduct['property_id']);

        $this->assertDatabaseMissing('products', $oldProduct);
        $this->assertDatabaseHas('products', $newProduct);
        //продумать необходимость этой проверки
        // $this->assertDatabaseHas(
        //     'product_property',
        //     [
        //         'product_id' => $product->id,
        //         'property_id' => $property->id,
        //     ]
        // );
    }
}

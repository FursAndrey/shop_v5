<?php

namespace Tests\Feature;

use App\Actions\CreateCategoryAction;
use App\Actions\CreateImageAction;
use App\Actions\CreateOptionAction;
use App\Actions\CreatePrductAction;
use App\Actions\CreatePropertyAction;
use App\Actions\CreateSkuAction;
use App\Actions\TestingActions\CreateTestSkuOptionRelationAction;
use App\Actions\TestingActions\GetTestCategoryAction;
use App\Actions\TestingActions\GetTestImageAction;
use App\Actions\TestingActions\GetTestInsertedSkuIDAction;
use App\Actions\TestingActions\GetTestOptionAction;
use App\Actions\TestingActions\GetTestProductAction;
use App\Actions\TestingActions\GetTestPropertyAction;
use App\Actions\TestingActions\GetTestSkuWithImageAction;
use App\Actions\TestingActions\GetTestSkuWithoutImageAction;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SkuTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_status_200()
    {
        $response = $this->get('/api/skus');

        $response->assertStatus(200);
    }

    public function test_index_page_json_without_data()
    {
        $response = $this->get('/api/skus');

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
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );
        (new CreateTestSkuOptionRelationAction)($sku->id, $option->id);

        $response = $this->get('/api/skus');
        
        $response->assertJsonFragment(
            [
                'id' => $sku->id,
                'count' => $sku->count,
                'price' => $sku->price,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                ],
                'options' => [
                    [
                        'id' => $option->id,
                        'name' => $option->name,
                    ]
                ],
                'images' => [],
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
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );

        $response = $this->get('/api/skus/'.$sku->id);

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
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );

        $response = $this->get('/api/skus/'.$sku->id);

        $response->assertJsonPath('count', $sku->count);
    }

    public function test_store_without_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        
        $sku = (new GetTestSkuWithoutImageAction)($product->id, $option->id);

        $this->assertDatabaseCount('skus', 0);
        $this->post('/api/skus', $sku);

        $this->assertDatabaseCount('skus', 1);

        unset($sku['option_id']);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseHas(
            'option_sku', 
            [
                'option_id' => $option->id,
            ]
        );
    }

    public function test_store_with_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        
        $sku = (new GetTestSkuWithImageAction)($product->id, $option->id, $file);
        
        $this->assertDatabaseCount('skus', 0);
        Storage::disk('public')->assertMissing('uploads/'.$file->hashName());
        $this->post('/api/skus', $sku);

        $this->assertDatabaseCount('skus', 1);

        unset($sku['option_id']);
        unset($sku['img']);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseHas(
            'option_sku', 
            [
                'option_id' => $option->id,
            ]
        );
        Storage::disk('public')->assertExists('uploads/'.$file->hashName());
    }

    public function test_destroy_without_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );
        (new CreateTestSkuOptionRelationAction)($sku->id, $option->id);
        
        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->delete('/api/skus/'.$sku->id);
        $this->assertDatabaseMissing('skus', ['id' => $sku->id]);
    }
    
    public function test_destroy_with_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );
        (new CreateTestSkuOptionRelationAction)($sku->id, $option->id);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        $image = (new CreateImageAction)((new GetTestImageAction)($sku->id, $file));
        
        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->assertDatabaseHas('images', ['id' => $image->id]);
        $this->delete('/api/skus/'.$sku->id);
        $this->assertDatabaseMissing('skus', ['id' => $sku->id]);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_update_put_with_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');
        
        $oldSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $oldFile);
        $this->post('/api/skus', $oldSku);

        $insertedSkuId = (new GetTestInsertedSkuIDAction)($this->get('/api/skus'));
        
        unset($oldSku['option_id']);
        unset($oldSku['img']);

        $this->assertDatabaseHas('skus', $oldSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $newFile);
        $this->put('/api/skus/'.$insertedSkuId, $newSku);

        unset($newSku['option_id']);
        unset($newSku['img']);

        $this->assertDatabaseMissing('skus', $oldSku);
        $this->assertDatabaseHas('skus', $newSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertExists('uploads/'.$newFile->hashName());
    }

    public function test_update_patch_with_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreatePrductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');
        
        $oldSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $oldFile);
        $this->post('/api/skus', $oldSku);

        $insertedSkuId = (new GetTestInsertedSkuIDAction)($this->get('/api/skus'));
        
        unset($oldSku['option_id']);
        unset($oldSku['img']);

        $this->assertDatabaseHas('skus', $oldSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $newFile);
        $this->patch('/api/skus/'.$insertedSkuId, $newSku);

        unset($newSku['option_id']);
        unset($newSku['img']);

        $this->assertDatabaseMissing('skus', $oldSku);
        $this->assertDatabaseHas('skus', $newSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertExists('uploads/'.$newFile->hashName());
    }
}

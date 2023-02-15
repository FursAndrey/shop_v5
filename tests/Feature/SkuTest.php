<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestCategoryAction;
use App\Actions\TestingActions\Create\CreateTestImageAction;
use App\Actions\TestingActions\Create\CreateTestOptionAction;
use App\Actions\TestingActions\Create\CreateTestProductAction;
use App\Actions\TestingActions\Create\CreateTestProductPropertyRelationAction;
use App\Actions\TestingActions\Create\CreateTestPropertyAction;
use App\Actions\TestingActions\Create\CreateTestSkuAction;
use App\Actions\TestingActions\Create\CreateTestSkuOptionRelationAction;

use App\Actions\TestingActions\Get\GetTestCategoryAction;
use App\Actions\TestingActions\Get\GetTestImageAction;
use App\Actions\TestingActions\Get\GetTestInsertedSkuIDAction;
use App\Actions\TestingActions\Get\GetTestOptionAction;
use App\Actions\TestingActions\Get\GetTestProductAction;
use App\Actions\TestingActions\Get\GetTestPropertyAction;
use App\Actions\TestingActions\Get\GetTestSkuWithImageAction;
use App\Actions\TestingActions\Get\GetTestSkuWithoutImageAction;

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
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        (new CreateTestProductPropertyRelationAction)($property->id, $product->id);
        $sku = (new CreateTestSkuAction)(
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
                    'properties' => [
                        [
                            'id' => $property->id,
                            'name' => $property->name,
                        ]
                    ]
                ],
                'options' => [
                    [
                        'id' => $option->id,
                        'name' => $option->name,
                        'property' => [
                            'id' => $property->id,
                            'name' => $property->name,
                        ]
                    ]
                ],
                'images' => [],
            ]
        );
    }

    public function test_show_page_status_200()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateTestSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );

        $response = $this->get('/api/skus/'.$sku->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        (new CreateTestProductPropertyRelationAction)($property->id, $product->id);
        $sku = (new CreateTestSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );
        (new CreateTestSkuOptionRelationAction)($sku->id, $option->id);

        $response = $this->get('/api/skus/'.$sku->id);

        $response->assertJsonFragment(
            [
                'id' => $sku->id,
                'count' => $sku->count,
                'price' => $sku->price,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'properties' => [
                        [
                            'id' => $property->id,
                            'name' => $property->name,
                        ]
                    ]
                ],
                'options' => [
                    [
                        'id' => $option->id,
                        'name' => $option->name,
                        'property' => [
                            'id' => $property->id,
                            'name' => $property->name,
                        ]
                    ]
                ],
                'images' => [],
            ]
        );
    }

    public function test_store_without_images()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
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
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
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
        unset($sku['image']);

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
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateTestSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );
        (new CreateTestSkuOptionRelationAction)($sku->id, $option->id);

        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->delete('/api/skus/'.$sku->id);
        $this->assertDatabaseMissing('skus', ['id' => $sku->id]);
    }

    public function test_destroy_with_images()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );
        $sku = (new CreateTestSkuAction)(
            (new GetTestSkuWithoutImageAction)($product->id, $option->id)
        );
        (new CreateTestSkuOptionRelationAction)($sku->id, $option->id);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        $image = (new CreateTestImageAction)((new GetTestImageAction)($sku->id, $file->hashName()));

        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->assertDatabaseHas('images', ['id' => $image->id]);
        $this->delete('/api/skus/'.$sku->id);
        $this->assertDatabaseMissing('skus', ['id' => $sku->id]);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_update_put_with_images()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );

        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');

        $oldSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $oldFile);
        $this->post('/api/skus', $oldSku);

        $insertedSkuId = (new GetTestInsertedSkuIDAction)($this->get('/api/skus'));

        unset($oldSku['option_id']);
        unset($oldSku['image']);

        $this->assertDatabaseHas('skus', $oldSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $newFile);
        $this->put('/api/skus/'.$insertedSkuId, $newSku);

        unset($newSku['option_id']);
        unset($newSku['image']);

        $this->assertDatabaseMissing('skus', $oldSku);
        $this->assertDatabaseHas('skus', $newSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertExists('uploads/'.$newFile->hashName());
    }

    public function test_update_patch_with_images()
    {
        $property = (new CreateTestPropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = (new CreateTestOptionAction)(
            (new GetTestOptionAction)($property->id)
        );
        $category = (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)($property->id, $category->id)
        );

        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');

        $oldSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $oldFile);
        $this->post('/api/skus', $oldSku);

        $insertedSkuId = (new GetTestInsertedSkuIDAction)($this->get('/api/skus'));

        unset($oldSku['option_id']);
        unset($oldSku['image']);

        $this->assertDatabaseHas('skus', $oldSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = (new GetTestSkuWithImageAction)($product->id, $option->id, $newFile);
        $this->patch('/api/skus/'.$insertedSkuId, $newSku);

        unset($newSku['option_id']);
        unset($newSku['image']);

        $this->assertDatabaseMissing('skus', $oldSku);
        $this->assertDatabaseHas('skus', $newSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertExists('uploads/'.$newFile->hashName());
    }
}

<?php

namespace Tests\Feature;

use App\Actions\CreateCategoryAction;
use App\Actions\CreatePropertyAction;
use App\Actions\TestingActions\GetTestCategoryAction;
use App\Actions\TestingActions\GetTestPropertyAction;
use App\Models\Image;
use App\Models\Option;
use App\Models\Product;
use App\Models\Sku;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        $sku = Sku::create(
            [
                'price' => 15.5,
                'count' => 4,
                'product_id' => $product->id,
                'option_id' => [
                    $option->id,
                ],
            ]
        );
        DB::table('option_sku')->insert(
            [
                ['sku_id'=>$sku->id, 'option_id'=>$option->id],
            ]
        );

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
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        $sku = Sku::create(
            [
                'price' => 15.5,
                'count' => 4,
                'product_id' => $product->id,
                'option_id' => [
                    $option->id,
                ],
            ]
        );

        $response = $this->get('/api/skus/'.$sku->id);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        $sku = Sku::create(
            [
                'price' => 15.5,
                'count' => 4,
                'product_id' => $product->id,
                'option_id' => [
                    $option->id,
                ],
            ]
        );

        $response = $this->get('/api/skus/'.$sku->id);

        $response->assertJsonPath('count', $sku->count);
    }

    public function test_store_without_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        
        $sku = [
            'price' => 15.5,
            'count' => 4,
            'product_id' => $product->id,
            'option_id' => [
                $option->id,
            ],
        ];

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
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        
        $sku = [
            'price' => 15.5,
            'count' => 4,
            'product_id' => $product->id,
            'option_id' => [
                $option->id,
            ],
            'img' => [
                $file
            ],
        ];
        
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
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        $sku = Sku::create(
            [
                'price' => 15.5,
                'count' => 4,
                'product_id' => $product->id,
                'option_id' => [
                    $option->id,
                ],
            ]
        );
        DB::table('option_sku')->insert(
            [
                ['sku_id'=>$sku->id, 'option_id'=>$option->id],
            ]
        );
        
        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->delete('/api/skus/'.$sku->id);
        $this->assertDatabaseMissing('skus', ['id' => $sku->id]);
    }
    
    public function test_destroy_with_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        $sku = Sku::create(
            [
                'price' => 15.5,
                'count' => 4,
                'product_id' => $product->id,
                'option_id' => [
                    $option->id,
                ],
            ]
        );
        DB::table('option_sku')->insert(
            [
                ['sku_id'=>$sku->id, 'option_id'=>$option->id],
            ]
        );

        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        $image = Image::create([
            'sku_id' => $sku->id,
            'file' => $file->hashName()
        ]);
        
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
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');
        
        $oldSku = [
            'price' => 15.5,
            'count' => 4,
            'product_id' => $product->id,
            'option_id' => [
                $option->id,
            ],
            'img' => [
                $oldFile
            ],
        ];
        $this->post('/api/skus', $oldSku);

        $response = $this->get('/api/skus');

        $insertedSkuId = $response->original[0]->id;
        
        $this->assertDatabaseHas('skus', ['price' => $oldSku['price']]);
        $this->assertDatabaseHas('skus', ['count' => $oldSku['count']]);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = [
            'price' => 1.5,
            'count' => 45,
            'product_id' => $product->id,
            'option_id' => [
                $option->id,
            ],
            'img' => [
                $newFile
            ],
        ];
        $this->put('/api/skus/'.$insertedSkuId, $newSku);

        $this->assertDatabaseMissing('skus', ['price' => $oldSku['price']]);
        $this->assertDatabaseMissing('skus', ['count' => $oldSku['count']]);
        $this->assertDatabaseHas('skus', ['price' => $newSku['price']]);
        $this->assertDatabaseHas('skus', ['count' => $newSku['count']]);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertExists('uploads/'.$newFile->hashName());
    }

    public function test_update_patch_with_images()
    {
        $property = (new CreatePropertyAction)(
            (new GetTestPropertyAction)()
        );
        $option = Option::create(
            [
                'name' => 'opti',
                'property_id' => $property->id,
            ]
        );
        $category = (new CreateCategoryAction)(
            (new GetTestCategoryAction)()
        );
        $product = Product::create(
            [
                'name' => 'prod',
                'description' => 'description',
                'category_id' => $category->id,
                'property_id' => [
                    $property->id,
                ],
            ]
        );
        
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');
        
        $oldSku = [
            'price' => 15.5,
            'count' => 4,
            'product_id' => $product->id,
            'option_id' => [
                $option->id,
            ],
            'img' => [
                $oldFile
            ],
        ];
        $this->post('/api/skus', $oldSku);

        $response = $this->get('/api/skus');

        $insertedSkuId = $response->original[0]->id;
        
        $this->assertDatabaseHas('skus', ['price' => $oldSku['price']]);
        $this->assertDatabaseHas('skus', ['count' => $oldSku['count']]);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = [
            'price' => 1.5,
            'count' => 45,
            'product_id' => $product->id,
            'option_id' => [
                $option->id,
            ],
            'img' => [
                $newFile
            ],
        ];
        $this->patch('/api/skus/'.$insertedSkuId, $newSku);

        $this->assertDatabaseMissing('skus', ['price' => $oldSku['price']]);
        $this->assertDatabaseMissing('skus', ['count' => $oldSku['count']]);
        $this->assertDatabaseHas('skus', ['price' => $newSku['price']]);
        $this->assertDatabaseHas('skus', ['count' => $newSku['count']]);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertExists('uploads/'.$newFile->hashName());
    }
}

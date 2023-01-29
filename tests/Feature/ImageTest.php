<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestCategoryAction;
use App\Actions\TestingActions\Create\CreateTestImageAction;
use App\Actions\TestingActions\Create\CreateTestOptionAction;
use App\Actions\TestingActions\Create\CreateTestProductAction;
use App\Actions\TestingActions\Create\CreateTestPropertyAction;
use App\Actions\TestingActions\Create\CreateTestSkuAction;
use App\Actions\TestingActions\Create\CreateTestSkuOptionRelationAction;

use App\Actions\TestingActions\Get\GetTestCategoryAction;
use App\Actions\TestingActions\Get\GetTestImageAction;
use App\Actions\TestingActions\Get\GetTestOptionAction;
use App\Actions\TestingActions\Get\GetTestProductAction;
use App\Actions\TestingActions\Get\GetTestPropertyAction;
use App\Actions\TestingActions\Get\GetTestSkuWithoutImageAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_one_image()
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

        $this->delete('/api/images/'.$image->id);

        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_destroy_several_images()
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
        $file1 = UploadedFile::fake()->image('test.jpg');
        $image1 = (new CreateTestImageAction)((new GetTestImageAction)($sku->id, $file1->hashName()));
        $file2 = UploadedFile::fake()->image('test.jpg');
        $image2 = (new CreateTestImageAction)((new GetTestImageAction)($sku->id, $file2->hashName()));
        $file3 = UploadedFile::fake()->image('test.jpg');
        $image3 = (new CreateTestImageAction)((new GetTestImageAction)($sku->id, $file3->hashName()));

        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->assertDatabaseHas('images', ['id' => $image1->id]);
        $this->assertDatabaseHas('images', ['id' => $image2->id]);
        $this->assertDatabaseHas('images', ['id' => $image3->id]);

        $this->delete('/api/images/all/'.$sku->id);

        $this->assertDatabaseHas('skus', ['id' => $sku->id]);
        $this->assertDatabaseMissing('images', ['id' => $image1->id]);
        $this->assertDatabaseMissing('images', ['id' => $image2->id]);
        $this->assertDatabaseMissing('images', ['id' => $image3->id]);
    }
}

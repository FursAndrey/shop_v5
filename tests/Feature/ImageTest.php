<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestImageAction;

use App\Actions\TestingActions\Get\GetTestImageAction;
use App\Actions\TestingActions\Prepare\PrepareTestSkuAction;
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
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');

        $sku = (new PrepareTestSkuAction)->short();

        $image = (new CreateTestImageAction)((new GetTestImageAction)($sku['id'], $file->hashName()));

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseHas('images', ['id' => $image->id]);

        $this->delete('/api/images/'.$image->id);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_destroy_several_images()
    {
        $sku = (new PrepareTestSkuAction)->short();

        Storage::fake('public');
        $file1 = UploadedFile::fake()->image('test.jpg');
        $image1 = (new CreateTestImageAction)((new GetTestImageAction)($sku['id'], $file1->hashName()));
        $file2 = UploadedFile::fake()->image('test.jpg');
        $image2 = (new CreateTestImageAction)((new GetTestImageAction)($sku['id'], $file2->hashName()));
        $file3 = UploadedFile::fake()->image('test.jpg');
        $image3 = (new CreateTestImageAction)((new GetTestImageAction)($sku['id'], $file3->hashName()));

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseHas('images', ['id' => $image1->id]);
        $this->assertDatabaseHas('images', ['id' => $image2->id]);
        $this->assertDatabaseHas('images', ['id' => $image3->id]);

        $this->delete('/api/images/all/'.$sku['id']);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseMissing('images', ['id' => $image1->id]);
        $this->assertDatabaseMissing('images', ['id' => $image2->id]);
        $this->assertDatabaseMissing('images', ['id' => $image3->id]);
    }
    
    public function test_store_images()
    {
        $sku = (new PrepareTestSkuAction)->short();
        
        $this->assertDatabaseHas('skus', $sku);
        
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        Storage::disk('public')->assertMissing('uploads/'.$file->hashName());
        $this->assertDatabaseMissing('images', ['sku_id' => $sku['id']]);

        $this->post('/api/images/'.$sku['id'], ['image' => $file]);
        
        Storage::disk('public')->assertExists('uploads/'.$file->hashName());
        $this->assertDatabaseHas('images', ['sku_id' => $sku['id']]);
    }
}

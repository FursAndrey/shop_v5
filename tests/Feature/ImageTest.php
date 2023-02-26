<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Prepare\PrepareTestImageAction;
use App\Actions\TestingActions\Prepare\PrepareTestSkuAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_one_image()
    {
        $sku = (new PrepareTestSkuAction)->short();
        $image = (new PrepareTestImageAction)->short($sku['id']);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseHas('images', $image);

        $this->delete('/api/images/'.$image['id']);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseMissing('images', $image);
    }

    public function test_destroy_several_images()
    {
        $sku = (new PrepareTestSkuAction)->short();

        $image1 = (new PrepareTestImageAction)->short($sku['id']);
        $image2 = (new PrepareTestImageAction)->short($sku['id']);
        $image3 = (new PrepareTestImageAction)->short($sku['id']);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseHas('images', $image1);
        $this->assertDatabaseHas('images', $image2);
        $this->assertDatabaseHas('images', $image3);

        $this->delete('/api/images/all/'.$sku['id']);

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseMissing('images', $image1);
        $this->assertDatabaseMissing('images', $image2);
        $this->assertDatabaseMissing('images', $image3);
    }
    
    public function test_store_images()
    {
        $sku = (new PrepareTestSkuAction)->short();
        
        $this->assertDatabaseHas('skus', $sku);
        
        $file = (new PrepareTestImageAction)->noDB();

        Storage::disk('public')->assertMissing('uploads/'.$file->hashName());
        $this->assertDatabaseMissing('images', ['sku_id' => $sku['id']]);

        $this->post('/api/images/'.$sku['id'], ['image' => $file]);
        
        Storage::disk('public')->assertExists('uploads/'.$file->hashName());
        $this->assertDatabaseHas('images', ['sku_id' => $sku['id']]);
    }
}

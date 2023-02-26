<?php

namespace Tests\Feature;

use App\Actions\TestingActions\Create\CreateTestImageAction;

use App\Actions\TestingActions\Get\GetTestImageAction;
use App\Actions\TestingActions\Get\GetTestInsertedSkuIDAction;

use App\Actions\TestingActions\Prepare\PrepareTestSkuAction;

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
        $sku = (new PrepareTestSkuAction)->full();

        $response = $this->get('/api/skus');

        $response->assertJsonFragment($sku);
    }

    public function test_show_page_status_200()
    {
        $sku = (new PrepareTestSkuAction)->short();

        $response = $this->get('/api/skus/'.$sku['id']);

        $response->assertStatus(200);
    }

    public function test_show_page_json_data()
    {
        $sku = (new PrepareTestSkuAction)->full();

        $response = $this->get('/api/skus/'.$sku['id']);

        $response->assertJsonFragment($sku);
    }

    public function test_store_without_images()
    {
        $sku = (new PrepareTestSkuAction)->noDB();

        $this->assertDatabaseCount('skus', 0);
        $this->post('/api/skus', $sku);

        $this->assertDatabaseCount('skus', 1);

        unset($sku['option_id']);

        $this->assertDatabaseHas('skus', $sku);
        // $this->assertDatabaseHas(
        //     'option_sku',
        //     [
        //         'option_id' => $option->id,
        //     ]
        // );
    }

    public function test_store_with_images()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');
        $sku = (new PrepareTestSkuAction)->noDbImage($file);

        $this->assertDatabaseCount('skus', 0);
        Storage::disk('public')->assertMissing('uploads/'.$file->hashName());
        $this->post('/api/skus', $sku);

        $this->assertDatabaseCount('skus', 1);

        unset($sku['option_id']);
        unset($sku['image']);

        $this->assertDatabaseHas('skus', $sku);
        // $this->assertDatabaseHas(
        //     'option_sku',
        //     [
        //         'option_id' => $option->id,
        //     ]
        // );
        Storage::disk('public')->assertExists('uploads/'.$file->hashName());
    }

    public function test_destroy_without_images()
    {
        $sku = (new PrepareTestSkuAction)->short();

        $this->assertDatabaseHas('skus', $sku);
        $this->delete('/api/skus/'.$sku['id']);
        $this->assertDatabaseMissing('skus', $sku);
    }

    public function test_destroy_with_images()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('test.jpg');

        $sku = (new PrepareTestSkuAction)->short();

        $image = (new CreateTestImageAction)((new GetTestImageAction)($sku['id'], $file->hashName()));

        $this->assertDatabaseHas('skus', $sku);
        $this->assertDatabaseHas('images', ['id' => $image->id]);
        $this->delete('/api/skus/'.$sku['id']);
        $this->assertDatabaseMissing('skus', $sku);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_update_put_with_images()
    {
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');

        $oldSku = (new PrepareTestSkuAction)->noDbImage($oldFile);
        $this->post('/api/skus', $oldSku);

        $insertedSkuId = (new GetTestInsertedSkuIDAction)($this->get('/api/skus'));

        unset($oldSku['option_id']);
        unset($oldSku['image']);

        $this->assertDatabaseHas('skus', $oldSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = (new PrepareTestSkuAction)->noDbImage($newFile);
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
        Storage::fake('public');
        $oldFile = UploadedFile::fake()->image('test.jpg');
        $newFile = UploadedFile::fake()->image('test.jpg');

        $oldSku = (new PrepareTestSkuAction)->noDbImage($oldFile);
        $this->post('/api/skus', $oldSku);

        $insertedSkuId = (new GetTestInsertedSkuIDAction)($this->get('/api/skus'));

        unset($oldSku['option_id']);
        unset($oldSku['image']);

        $this->assertDatabaseHas('skus', $oldSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertMissing('uploads/'.$newFile->hashName());

        $newSku = (new PrepareTestSkuAction)->noDbImage($newFile);
        $this->patch('/api/skus/'.$insertedSkuId, $newSku);

        unset($newSku['option_id']);
        unset($newSku['image']);

        $this->assertDatabaseMissing('skus', $oldSku);
        $this->assertDatabaseHas('skus', $newSku);
        Storage::disk('public')->assertExists('uploads/'.$oldFile->hashName());
        Storage::disk('public')->assertExists('uploads/'.$newFile->hashName());
    }
}

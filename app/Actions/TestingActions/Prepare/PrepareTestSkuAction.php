<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestSkuAction;
use App\Actions\TestingActions\Create\CreateTestSkuOptionRelationAction;
use App\Actions\TestingActions\Get\GetTestSkuWithoutImageAction;
use Illuminate\Http\Testing\File;
use App\Models\Sku;

class PrepareTestSkuAction
{
    private static $product = null;
    private static $property = null;
    private static $category = null;
    private static $option = null;

    public function __construct()
    {
        if (is_null(self::$option)) {
            self::$option = (new PrepareTestOptionAction)->full();
        }

        if (is_null(self::$product)) {
            $prepareProduct = (new PrepareTestProductAction);
            self::$product = $prepareProduct->short();

            self::$property = $prepareProduct->getDependencies()['property'];
            self::$category = $prepareProduct->getDependencies()['category'];
        }
    }

    public function __destruct()
    {
        self::$product = null;
        self::$property = null;
        self::$category = null;
        self::$option = null;
    }

    public function short(): array
    {
        $sku = $this->intoDB();

        $arr = [
            'id' => $sku->id,
            'count' => $sku->count,
            'price' => $sku->price,
        ];

        return $arr;
    }

    public function full(): array
    {
        $sku = $this->intoDB();

        $arr = [
            'id' => $sku->id,
            'count' => $sku->count,
            'price' => $sku->price,
            'product' => [
                'id' => self::$product['id'],
                'name' => self::$product['name'],
                'description' => self::$product['description'],
                'properties' => [
                    [
                        'id' => self::$property['id'],
                        'name' => self::$property['name'],
                    ]
                ]
            ],
            'options' => [self::$option],
            'images' => [],
        ];

        return $arr;
    }

    public function getProductWithSku(): array
    {
        $sku = $this->intoDB();

        $arr = [
            'id' => self::$product['id'],
            'name' => self::$product['name'],
            'description' => self::$product['description'],
            'category' => [
                'id' => self::$category['id'],
                'name' => self::$category['name'],
            ],
            'properties' => [
                [
                    'id' => self::$property['id'],
                    'name' => self::$property['name'],
                ]
            ],
            'skus' => [
                [
                    'id' => $sku->id,
                    'count' => $sku->count,
                    'price' => $sku->price,
                    'options' => [self::$option],
                ]
            ]
        ];

        return $arr;
    }

    public function noDB(): array
    {
        return (new GetTestSkuWithoutImageAction)(self::$product['id'], self::$option['id']);
    }

    public function noDbImage(File $file): array
    {
        $sku = $this->noDB();
        $sku['image'] = [
            $file
        ];

        return $sku;
    }

    private function intoDB(): Sku
    {
        $sku = (new CreateTestSkuAction)($this->noDB());        
        (new CreateTestSkuOptionRelationAction)($sku->id, self::$option['id']);

        return $sku;
    }
}
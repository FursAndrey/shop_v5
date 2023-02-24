<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestProductAction;
use App\Actions\TestingActions\Create\CreateTestProductPropertyRelationAction;
use App\Actions\TestingActions\Get\GetTestProductAction;
use App\Models\Product;

class PrepareTestProductAction
{
    private static $property = null;
    private static $category = null;

    public function __construct()
    {
        if (is_null(self::$property)) {
            self::$property = (new PrepareTestPropertyAction)->short();
        }

        if (is_null(self::$category)) {
            self::$category = (new PrepareTestCategoryAction)->short();
        }
    }

    public function __destruct()
    {
        self::$property = null;
        self::$category = null;
    }

    public function short(): array
    {
        $product = $this->intoDB();

        $arr = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
        ];

        return $arr;
    }

    public function full(): array
    {
        $product = $this->intoDB();

        $arr = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
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
        ];

        return $arr;
    }

    public function noDB(): array
    {
        return (new GetTestProductAction)(self::$property['id'], self::$category['id']);
    }

    private function intoDB(): Product
    {
        $product = (new CreateTestProductAction)(
            (new GetTestProductAction)(self::$property['id'], self::$category['id'])
        );
        (new CreateTestProductPropertyRelationAction)(self::$property['id'], $product->id);

        return $product;
    }

    public function getDependencies()
    {
        return [
            'category' => self::$category,
            'property' => self::$property,
        ];
    }
}
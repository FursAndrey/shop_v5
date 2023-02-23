<?php

namespace App\Actions\TestingActions\Prepare;

use App\Actions\TestingActions\Create\CreateTestCategoryAction;
use App\Actions\TestingActions\Get\GetTestCategoryAction;
use App\Models\Category;

class PrepareTestCategoryAction
{
    public function short(): array
    {
        $category = $this->getProperty();

        $arr = [
            'id' => $category->id,
            'name' => $category->name,
        ];

        return $arr;
    }

    public function full(): array
    {
        $category = $this->getProperty();

        $arr = [
            'id' => $category->id,
            'name' => $category->name,
            'products' => [],
        ];

        return $arr;
    }

    public function noDB(): array
    {
        return (new GetTestCategoryAction)();
    }

    private function getProperty(): Category
    {
        return (new CreateTestCategoryAction)(
            (new GetTestCategoryAction)()
        );
    }
}
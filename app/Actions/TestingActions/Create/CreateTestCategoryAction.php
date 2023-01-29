<?php

namespace App\Actions\TestingActions\Create;

use App\Models\Category;

class CreateTestCategoryAction
{
    public function __invoke(array $category): Category
    {
        return Category::create($category);
    }
}
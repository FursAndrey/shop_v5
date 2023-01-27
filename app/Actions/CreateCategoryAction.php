<?php

namespace App\Actions;

use App\Models\Category;

class CreateCategoryAction
{
    public function __invoke(array $category): Category
    {
        return Category::create($category);
    }
}
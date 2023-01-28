<?php

namespace App\Actions\TestingActions;

use Illuminate\Support\Facades\DB;

class CreateTestProductPropertyRelationAction
{
    public function __invoke(int $property_id, int $product_id): void
    {
        DB::table('product_property')->insert(
            [
                ['property_id'=>$property_id, 'product_id'=>$product_id],
            ]
        );
    }
}
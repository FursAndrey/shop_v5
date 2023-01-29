<?php

namespace App\Actions\TestingActions\Create;

use Illuminate\Support\Facades\DB;

class CreateTestSkuOptionRelationAction
{
    public function __invoke(int $sku_id, int $option_id): void
    {
        DB::table('option_sku')->insert(
            [
                ['sku_id'=>$sku_id, 'option_id'=>$option_id],
            ]
        );
    }
}
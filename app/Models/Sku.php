<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sku extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id',
        'price',
        'count',
    ];

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options():BelongsToMany
    {
        return $this->belongsToMany(Option::class)->withTimestamps();
    }
}

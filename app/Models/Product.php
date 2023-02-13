<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
    ];

    public function category():BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function properties():BelongsToMany
    {
        return $this->belongsToMany(Property::class)->withTimestamps();;
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class);
    }
}

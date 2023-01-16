<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Option extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'property_id',
    ];

    public function property():BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function skus():BelongsToMany
    {
        return $this->belongsToMany(Sku::class);
    }
}

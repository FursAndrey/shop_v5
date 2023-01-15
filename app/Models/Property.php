<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
    ];

    public function products():BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();;
    }

    public function options():HasMany
    {
        return $this->hasMany(Option::class);
    }
}

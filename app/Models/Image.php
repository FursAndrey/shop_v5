<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'file',
        'sku_id',
    ];

    public function sku(): BelongsTo
    {
        return $this->belongsTo(Sku::class);
    }

    public function setFileAttribute(string $imgUrl): void
    {
        $this->attributes['file'] = preg_replace('/^[A-Za-z0-9_]+\/{1}/', '', $imgUrl);
    }

    public function getFileForDeleteAttribute(): string
    {
        return str_replace('/', '\\', 'storage/uploads/'.$this->file);
    }

    public function getFileForViewAttribute(): string
    {
        return url('/storage/uploads/'.$this->file);
    }
}

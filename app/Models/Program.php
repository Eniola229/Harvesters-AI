<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasUuids;

    protected $fillable = [
        'title',
        'description',
        'image_url',
        'image_public_id',
        'start_date',
        'end_date',
        'venue',
        'campus',
        'is_active',
        'is_featured',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function getMetaValueAttribute(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }
}
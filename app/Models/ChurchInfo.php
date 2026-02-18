<?php
// Location: app/Models/ChurchInfo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ChurchInfo extends Model
{
    use HasUuids;

    protected $fillable = [
        'category', 'title', 'content', 'is_active', 'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('category', $category)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }
}
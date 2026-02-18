<?php
// Location: app/Models/Leader.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leader extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'title', 'phone', 'email', 'bio',
        'image_url', 'campus_id', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }
}
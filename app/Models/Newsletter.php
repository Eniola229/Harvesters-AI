<?php
// Location: app/Models/Newsletter.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasUuids;

    protected $fillable = [
        'title', 'message', 'media_url', 'media_type',
        'media_public_id', 'target_campus', 'sent_count',
        'status', 'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }
}
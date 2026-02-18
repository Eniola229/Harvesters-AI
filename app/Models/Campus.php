<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campus extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'address', 'city', 'state', 'country',
        'pastor_name', 'pastor_phone', 'service_times',
        'image_url', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function leaders(): HasMany
    {
        return $this->hasMany(Leader::class);
    }
}
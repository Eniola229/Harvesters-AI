<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasUuids;

    protected $fillable = [
        'member_id',
        'phone',
        'channel',
        'context',
        'state',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(ChurchMember::class, 'member_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function addToContext(string $role, string $content): void
    {
        $context = $this->context ?? [];
        $context[] = ['role' => $role, 'content' => $content];

        // Keep only last 20 messages for context window management
        if (count($context) > 20) {
            $context = array_slice($context, -20);
        }

        $this->update(['context' => $context]);
    }
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChurchMember extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'phone',
        'channel',
        'morning_alert',
        'alert_time',
        'campus',
        'is_active',
        'last_interaction_at',
    ];

    protected $casts = [
        'morning_alert' => 'boolean',
        'is_active' => 'boolean',
        'last_interaction_at' => 'datetime',
    ];

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'member_id');
    }

    public function latestConversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'member_id')->latestOfMany();
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        // Normalize to E.164 format
        if (str_starts_with($phone, 'whatsapp:')) {
            return $phone;
        }
        return $phone;
    }
}
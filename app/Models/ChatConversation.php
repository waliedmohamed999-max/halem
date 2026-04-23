<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_token',
        'visitor_name',
        'visitor_phone',
        'visitor_email',
        'status',
        'ai_enabled',
        'human_requested',
        'admin_unread_count',
        'customer_unread_count',
        'assigned_user_id',
        'last_message_at',
        'customer_last_seen_at',
        'admin_last_seen_at',
        'last_message_preview',
    ];

    protected $casts = [
        'ai_enabled' => 'boolean',
        'human_requested' => 'boolean',
        'last_message_at' => 'datetime',
        'customer_last_seen_at' => 'datetime',
        'admin_last_seen_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function markSeenByAdmin(): void
    {
        $this->forceFill([
            'admin_unread_count' => 0,
            'admin_last_seen_at' => now(),
        ])->save();
    }

    public function markSeenByCustomer(): void
    {
        $this->forceFill([
            'customer_unread_count' => 0,
            'customer_last_seen_at' => now(),
        ])->save();
    }
}

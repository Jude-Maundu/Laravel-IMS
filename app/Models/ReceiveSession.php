<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceiveSession extends Model
{
    protected $fillable = [
        'event_id',
        'session_token',
        'created_by',
        'expires_at',
        'status',
        'received_count',
        'completed_at',
        'receive_ref',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receivedPieces(): HasMany
    {
        return $this->hasMany(ReceiveSessionPiece::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '>', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at < now() || $this->status === 'expired';
    }

    public function isValid(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function generateReceiveRef(): string
    {
        $year = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'RCV-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}

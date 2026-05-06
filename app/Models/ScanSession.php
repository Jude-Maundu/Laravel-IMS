<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanSession extends Model
{
    protected $fillable = [
        'event_id',
        'session_token',
        'created_by',
        'expires_at',
        'status',
        'scanned_count',
        'completed_at',
        'dispatch_batch',
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

    public function scannedPieces()
    {
        return $this->hasMany(ScanSessionPiece::class);
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
}

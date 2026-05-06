<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiveSessionPiece extends Model
{
    protected $fillable = [
        'receive_session_id',
        'item_piece_id',
        'unique_code',
        'item_id',
        'condition_score',
        'destination',
        'damage_note',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function receiveSession(): BelongsTo
    {
        return $this->belongsTo(ReceiveSession::class);
    }

    public function itemPiece(): BelongsTo
    {
        return $this->belongsTo(ItemPiece::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}

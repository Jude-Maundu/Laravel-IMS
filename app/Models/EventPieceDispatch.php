<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPieceDispatch extends Model
{
    protected $fillable = [
        'event_id',
        'item_piece_id',
        'condition_on_dispatch',
        'condition_on_return',
        'return_destination',
        'dispatched_at',
        'returned_at',
        'dispatched_by',
        'returned_by',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function itemPiece(): BelongsTo
    {
        return $this->belongsTo(ItemPiece::class);
    }

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MissingItem extends Model
{
    protected $fillable = [
        'event_id',
        'item_piece_id',
        'unique_code',
        'item_id',
        'marked_by',
        'marked_at',
        'notes',
        'status',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function itemPiece(): BelongsTo
    {
        return $this->belongsTo(ItemPiece::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function marker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}

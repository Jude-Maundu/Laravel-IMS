<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventItem extends Model
{
    protected $fillable = [
        'event_id', 'item_id',
        'quantity_requested', 'quantity_dispatched', 'quantity_returned',
        'condition_on_dispatch', 'condition_on_return',
        'dispatch_notes', 'return_notes',
        'return_destination', 'return_processed',
        'dispatched_at', 'returned_at',
        'dispatched_by', 'returned_by',
        'dispatch_batch',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'returned_at'   => 'datetime',
        'return_processed' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(EventItemImage::class);
    }

    public function dispatchImages(): HasMany
    {
        return $this->hasMany(EventItemImage::class)->where('type', 'dispatch');
    }

    public function returnImages(): HasMany
    {
        return $this->hasMany(EventItemImage::class)->where('type', 'return');
    }

    public function dispatchedPieces()
    {
        return $this->hasMany(EventPieceDispatch::class, 'event_id', 'event_id')
            ->whereHas('itemPiece', function($query) {
                $query->where('item_id', $this->item_id);
            })
            ->with('itemPiece');
    }

    public function getConditionLabelAttribute(): string
    {
        return match((int) $this->condition_on_dispatch) {
            5 => 'Excellent',
            4 => 'Good',
            3 => 'Fair',
            2 => 'Average',
            1 => 'Poor',
            default => 'Not recorded',
        };
    }
}

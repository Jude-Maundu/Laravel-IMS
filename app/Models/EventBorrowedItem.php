<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventBorrowedItem extends Model
{
    protected $fillable = [
        'event_id',
        'item_name',
        'source_company',
        'quantity_dispatched',
        'quantity_returned',
        'notes',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}

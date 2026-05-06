<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventOperationalItem extends Model
{
    protected $fillable = [
        'event_id',
        'operational_item_id',
        'custom_name',
        'quantity_dispatched',
        'quantity_returned',
        'notes',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function operationalItem(): BelongsTo
    {
        return $this->belongsTo(OperationalItem::class);
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->operational_item_id === null) {
            return $this->custom_name;
        }

        return $this->operationalItem->name ?? '';
    }
}

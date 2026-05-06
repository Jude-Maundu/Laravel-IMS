<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventItemImage extends Model
{
    protected $fillable = [
        'event_item_id', 'image_path', 'type', 'uploaded_by',
    ];

    public function eventItem(): BelongsTo
    {
        return $this->belongsTo(EventItem::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}

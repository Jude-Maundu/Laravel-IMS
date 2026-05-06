<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPiece extends Model
{
    protected $fillable = [
        'item_id',
        'unique_code',
        'status',
        'condition_score',
        'current_event_id',
        'notes',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function currentEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'current_event_id');
    }

    public function getQrCodeSvg(): string
    {
        $url = url('/item/' . $this->unique_code);
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($url);
    }
}

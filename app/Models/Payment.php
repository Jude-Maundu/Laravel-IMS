<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'event_id',
        'amount',
        'phone',
        'status',
        'merchant_request_id',
        'checkout_request_id',
        'transaction_id',
        'response_data',
        'failure_reason',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}

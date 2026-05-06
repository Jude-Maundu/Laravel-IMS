<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checklist extends Model
{
    protected $fillable = [
        "item_id", "assignment_id", "action", "performed_by",
        "condition", "notes"
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }
}

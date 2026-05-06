<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assignment extends Model
{
    protected $fillable = [
        "item_id", "assigned_to", "assigned_by", "due_date",
        "returned_at", "status", "notes"
    ];

    protected $casts = [
        "due_date" => "date",
        "returned_at" => "date",
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }
}

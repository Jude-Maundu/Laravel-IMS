<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repair extends Model
{
    protected $fillable = [
        "item_id",
        "repair_type",
        "description",
        "materials_required",
        "damage_image_path",
        "estimated_cost",
        "actual_cost",
        "status",
        "started_at",
        "completed_at",
        "technician_name",
        "notes"
    ];

    protected $casts = [
        "estimated_cost" => "decimal:2",
        "actual_cost" => "decimal:2",
        "started_at" => "date",
        "completed_at" => "date",
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}

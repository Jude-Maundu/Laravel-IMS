<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    protected $fillable = [
        "name", "category", "brand", "model_number", "serial_number",
        "purchase_date", "purchase_cost", "specifications", "dimensions", "weight",
        "status", "location", "assigned_to", "assigned_by", "last_updated_by",
        "last_updated_at", "notes", "image_path", "total_pieces", "qr_code_svg"
    ];

    protected $casts = [
        "last_updated_at" => "datetime",
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($item) {
            if (!$item->qr_code_svg) {
                $item->update(['qr_code_svg' => $item->generateQrCodeSvg()]);
            }
        });
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, "item_id");
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class);
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ItemImage::class)->orderByDesc('is_primary')->orderBy('created_at');
    }

    public function primaryImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ItemImage::class)->where('is_primary', true)->latestOfMany();
    }

    public function getPrimaryImageUrlAttribute(): ?string
    {
        $primary = $this->images->firstWhere('is_primary', true);
        if ($primary) return asset('storage/' . $primary->image_path);
        if ($this->image_path) return asset('storage/' . $this->image_path);
        $first = $this->images->first();
        return $first ? asset('storage/' . $first->image_path) : null;
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_items')
            ->withPivot([
                'condition_on_dispatch', 'condition_on_return',
                'dispatched_at', 'returned_at',
            ])
            ->withTimestamps();
    }

    public function currentEvent(): ?\App\Models\Event
    {
        return $this->events()
            ->whereIn('events.status', ['Scheduled', 'Active', 'Set Down'])
            ->latest('event_items.created_at')
            ->first();
    }

    public function isAvailableForDispatch(): bool
    {
        return $this->status === 'Available';
    }

    public function pieces(): HasMany
    {
        return $this->hasMany(ItemPiece::class);
    }

    public static function generateShortCode(string $itemName): string
    {
        // Strip common words
        $commonWords = ['and', 'of', 'the', 'with', 'a', 'an', 'for', 'to', 'in'];
        $words = explode(' ', strtolower($itemName));
        $words = array_filter($words, fn($w) => !in_array($w, $commonWords) && strlen($w) > 0);

        // Extract first 3 meaningful letters
        $code = '';
        foreach ($words as $word) {
            $code .= substr($word, 0, max(1, 3 - strlen($code)));
            if (strlen($code) >= 3) break;
        }

        // Pad if needed
        $code = str_pad($code, 3, 'X');

        return strtoupper(substr($code, 0, 3));
    }

    public function generatePieces(int $count): void
    {
        $currentCount = $this->pieces()->count();

        if ($count <= $currentCount) {
            return; // Don't delete existing pieces
        }

        $shortCode = self::generateShortCode($this->name);
        $piecesToCreate = $count - $currentCount;

        // Get the highest piece number for this shortCode across all items
        $lastPiece = ItemPiece::where('unique_code', 'LIKE', "GA-{$shortCode}-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(unique_code, '-', -1) AS UNSIGNED) DESC")
            ->first();

        $startNumber = 1;
        if ($lastPiece) {
            // Extract the number from the last unique_code (format: GA-XXX-###)
            preg_match('/GA-[A-Z]{3}-(\d+)/', $lastPiece->unique_code, $matches);
            $startNumber = isset($matches[1]) ? (int)$matches[1] + 1 : 1;
        }

        for ($i = 0; $i < $piecesToCreate; $i++) {
            $number = $startNumber + $i;
            $uniqueCode = sprintf('GA-%s-%03d', $shortCode, $number);

            ItemPiece::create([
                'item_id' => $this->id,
                'unique_code' => $uniqueCode,
                'status' => 'Available',
            ]);
        }
    }

    public function getQrCodeSvg(): string
    {
        if ($this->qr_code_svg) {
            return $this->qr_code_svg;
        }

        return $this->generateQrCodeSvg();
    }

    public function generateQrCodeSvg(): string
    {
        $url = route('inventory.show', $this->id);
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($url);
    }
}

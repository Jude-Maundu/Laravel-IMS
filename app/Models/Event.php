<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'name', 'client_name', 'venue', 'location_name',
        'latitude', 'longitude',
        'loading_date', 'setup_date', 'event_date', 'setdown_date',
        'status', 'plan_ref', 'cost', 'notes', 'created_by',
        'linked_from_event_id', 'link_type',
        'payment_status', 'amount_due', 'customer_phone', 'transaction_id', 'customer_id',
    ];

    protected $casts = [
        'loading_date'  => 'date',
        'setup_date'    => 'date',
        'event_date'    => 'date',
        'setdown_date'  => 'date',
        'cost'          => 'decimal:2',
        'amount_due'    => 'decimal:2',
        'latitude'      => 'decimal:7',
        'longitude'     => 'decimal:7',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function eventItems(): HasMany
    {
        return $this->hasMany(EventItem::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'event_items')
            ->withPivot([
                'condition_on_dispatch', 'condition_on_return',
                'dispatch_notes', 'return_notes',
                'dispatched_at', 'returned_at',
                'dispatched_by', 'returned_by',
            ])
            ->withTimestamps();
    }

    public function staff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_staff')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function linkedFromEvent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'linked_from_event_id');
    }

    public function linkedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'linked_from_event_id');
    }

    public function scanSessions(): HasMany
    {
        return $this->hasMany(ScanSession::class);
    }

    public function receiveSessions(): HasMany
    {
        return $this->hasMany(ReceiveSession::class);
    }

    public function missingItems(): HasMany
    {
        return $this->hasMany(MissingItem::class);
    }

    public function eventPieceDispatches(): HasMany
    {
        return $this->hasMany(EventPieceDispatch::class);
    }

    public function borrowedItems(): HasMany
    {
        return $this->hasMany(EventBorrowedItem::class);
    }

    public function operationalItems(): HasMany
    {
        return $this->hasMany(EventOperationalItem::class);
    }

    public function getItemCountAttribute(): int
    {
        return $this->eventItems()->count();
    }

    public function hasActiveSession(): bool
    {
        return $this->scanSessions()
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->exists();
    }

    public function getActiveScanSession()
    {
        return $this->scanSessions()
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->first();
    }

    public function hasActiveReceiveSession(): bool
    {
        return $this->receiveSessions()
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->exists();
    }

    public function getActiveReceiveSession()
    {
        return $this->receiveSessions()
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->first();
    }

    public static function generateDispatchRef(): string
    {
        $year = now()->year;
        $count = ScanSession::whereYear('created_at', $year)->count() + 1;
        return 'DISP-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public static function generatePlanRef(): string
    {
        $year = now()->year;
        $count = Event::whereYear('created_at', $year)->count() + 1;
        return 'PLAN-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public static function getStatuses(): array
    {
        return ['Draft', 'Awaiting Payment', 'Scheduled', 'Active', 'Set Down', 'Completed', 'Cancelled'];
    }
}

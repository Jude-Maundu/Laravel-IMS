<?php

/**
 * Cleanup script for duplicate site-to-site draft events
 *
 * Run this script once to remove duplicate draft site-to-site events:
 * php artisan tinker < cleanup_duplicate_s2s_events.php
 */

use App\Models\Event;
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    // Find all site-to-site events grouped by parent event
    $duplicates = Event::where('link_type', 'site-to-site')
        ->where('status', 'Draft')
        ->whereNotNull('linked_from_event_id')
        ->orderBy('created_at', 'asc')
        ->get()
        ->groupBy('linked_from_event_id');

    $deletedCount = 0;
    $keptCount = 0;

    foreach ($duplicates as $parentId => $events) {
        if ($events->count() > 1) {
            // Keep the first one (oldest), delete the rest
            $keep = $events->first();
            $toDelete = $events->slice(1);

            echo "Parent Event ID {$parentId}: Found {$events->count()} linked events\n";
            echo "  - Keeping: {$keep->name} (ID: {$keep->id})\n";

            foreach ($toDelete as $duplicate) {
                echo "  - Deleting: {$duplicate->name} (ID: {$duplicate->id})\n";

                // Delete event items
                $duplicate->eventItems()->delete();

                // Delete team assignments
                $duplicate->staff()->detach();

                // Delete the event
                $duplicate->delete();

                $deletedCount++;
            }

            $keptCount++;
        }
    }

    echo "\n✅ Cleanup complete!\n";
    echo "   - Kept {$keptCount} unique site-to-site events\n";
    echo "   - Deleted {$deletedCount} duplicate events\n";
});

<?php

namespace App\Services;

use App\Models\ItemPiece;
use Illuminate\Support\Collection;
use Exception;

class PieceAvailabilityService
{
    /**
     * Returns count of available pieces for an item
     */
    public function getAvailableCount(int $itemId): int
    {
        return ItemPiece::where('item_id', $itemId)
                        ->where('status', 'Available')
                        ->count();
    }

    /**
     * Returns array of available piece records ordered by unique_code ASC
     */
    public function getAvailablePieces(int $itemId, int $limit = null): Collection
    {
        $query = ItemPiece::where('item_id', $itemId)
                          ->where('status', 'Available')
                          ->orderBy('unique_code', 'asc');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Validates a specific piece code for dispatch
     * Returns array: ['valid' => bool, 'message' => string, 'piece' => ItemPiece|null]
     * Checks: exists, correct item type, status is Available, not already in another dispatch
     */
    public function validatePieceForDispatch(string $uniqueCode, int $itemId): array
    {
        $piece = ItemPiece::where('unique_code', $uniqueCode)->first();

        if (!$piece) {
            return [
                'valid' => false,
                'message' => 'Piece code not found.',
                'piece' => null,
            ];
        }

        if ($piece->item_id !== $itemId) {
            return [
                'valid' => false,
                'message' => 'This piece belongs to a different item type.',
                'piece' => null,
            ];
        }

        if ($piece->status !== 'Available') {
            return [
                'valid' => false,
                'message' => "This piece is currently {$piece->status}.",
                'piece' => null,
            ];
        }

        if ($piece->current_event_id !== null) {
            return [
                'valid' => false,
                'message' => 'This piece is already assigned to another event.',
                'piece' => null,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Valid piece.',
            'piece' => $piece,
        ];
    }

    /**
     * Auto-assigns pieces for an item — finds next N available pieces ordered by unique_code
     * Returns collection of ItemPiece records
     * Throws exception if not enough available pieces
     */
    public function autoAssignPieces(int $itemId, int $quantity): Collection
    {
        $available = $this->getAvailableCount($itemId);

        if ($available < $quantity) {
            throw new Exception("Not enough available pieces. Requested: {$quantity}, Available: {$available}");
        }

        return $this->getAvailablePieces($itemId, $quantity);
    }

    /**
     * Executes the actual dispatch assignment:
     * Updates item_pieces status to Assigned
     * Sets current_event_id
     */
    public function dispatchPieces(
        Collection $pieces,
        int $eventId,
        int $conditionScore,
        int $dispatchedBy
    ): void {
        foreach ($pieces as $piece) {
            $piece->update([
                'status' => 'Assigned',
                'current_event_id' => $eventId,
                'condition_score' => $conditionScore,
            ]);
        }
    }
}

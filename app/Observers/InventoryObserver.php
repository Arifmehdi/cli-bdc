<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\InventoryLog;

class InventoryObserver
{
    /**
     * Handle the Inventory "created" event.
     */
    public function created(Inventory $inventory): void
    {
        //
    }

    /**
     * Handle the Inventory "updated" event.
     */
    public function updated(Inventory $inventory): void
    {
        \Log::info('Inventory updated: ', ['id' => $inventory->id]);

        $changedFields = $inventory->getChanges(); // Get updated fields
        $oldValues = $inventory->getOriginal();   // Get original data

        foreach ($changedFields as $field => $newValue) {
            // Skip timestamp fields
            if (in_array($field, ['updated_at', 'created_at'])) {
                continue;
            }

            // Create a log entry
            InventoryLog::create([
                'inventory_id' => $inventory->id,
                'changed_field' => $field,
                'old_value' => $oldValues[$field] ?? null,
                'new_value' => $newValue,
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Inventory "deleted" event.
     */
    public function deleted(Inventory $inventory): void
    {
        //
    }

    /**
     * Handle the Inventory "restored" event.
     */
    public function restored(Inventory $inventory): void
    {
        //
    }

    /**
     * Handle the Inventory "force deleted" event.
     */
    public function forceDeleted(Inventory $inventory): void
    {
        //
    }
}

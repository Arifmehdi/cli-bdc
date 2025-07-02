<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Inventory; // Replace with your model's namespace

class UpdateVisibilityStatus extends Command
{
    protected $signature = 'visibility:update';
    protected $description = 'Update visibility status based on active_till and created_at dates';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::now();
        // Fetch records where visibility needs to be updated
        Inventory::where(function ($query) use ($today) {
            // Check active_till condition
            $query->whereNotNull('active_till')
                  ->where('active_till', '<', $today->subMonth());
        })->orWhere(function ($query) use ($today) {
            // Fallback to created_at if active_till is null
            $query->whereNull('active_till')
                  ->where('created_at', '<', $today->subMonth());
        })->update(['is_visibility' => 0]);

        $this->info('Visibility status updated successfully.');
    }
}

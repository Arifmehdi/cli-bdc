<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserRollback extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last batch of user data based on batch_no';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the highest batch_no
        $lastBatchNo = DB::table('users')->max('batch_no');

        if ($lastBatchNo === null) {
            $this->info('No batch found to rollback.');
            return 0;
        }

        // Delete users with the last batch_no
        $deleted = DB::table('users')->where('batch_no', $lastBatchNo)->delete();

        // Provide feedback
        if ($deleted) {
            $this->info("Successfully rolled back batch_no: {$lastBatchNo}. Deleted {$deleted} records.");
        } else {
            $this->info("No records found for batch_no: {$lastBatchNo}.");
        }

        return 0;
    }
}

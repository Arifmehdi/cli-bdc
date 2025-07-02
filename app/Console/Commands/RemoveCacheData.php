<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RemoveCacheData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the cached dealers data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cache::forget('cached_dealers');

        $this->info('Dealers cache cleared successfully!');
    }
}

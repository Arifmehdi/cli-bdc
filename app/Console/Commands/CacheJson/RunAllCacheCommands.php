<?php

namespace App\Console\Commands\CacheJson;

use App\Models\CacheCommand;
use Illuminate\Console\Command;

class RunAllCacheCommands extends Command
{
    protected $signature = 'cache:run-all';
    protected $description = 'Run all active cache commands';

    public function handle()
    {
        $commands = CacheCommand::where('status', true)->get();

        foreach ($commands as $command) {
            $this->info("Running: {$command->name}");
            $this->call($command->command);
            $this->newLine();
        }

        $this->info('All cache commands executed successfully!');
    }
}

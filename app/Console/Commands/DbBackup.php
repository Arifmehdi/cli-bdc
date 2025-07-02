<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DbBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:db-backup';
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Database Backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup_".strtotime(now()).".sql";
        $db = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');
        // dd($db, $username, $password, $host);
        $command = "mysqldump --user=".env('DB_USERNAME')." --password".env('DB_PASSWORD')." --host=".env('DB_HOST')." ".env('DB_DATABASE')." > ".storage_path()."/app/backup".$filename;
        exec($command);
    }
}

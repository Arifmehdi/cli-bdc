<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
// use Log;

class BackupController extends Controller
{

    public function backupDatabase(Request $request)
    {
        $filename = "backup_extra_" . strtotime(now()) . ".sql";
        $db = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST');

        $command = "mysqldump --user={$username} --password={$password} --host={$host} {$db} > " . storage_path("app/backup/{$filename}");


        exec($command);

        return response()->json(['message' => 'Database backup completed successfully']);
    }
}

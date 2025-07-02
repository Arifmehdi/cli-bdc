<?php

use App\Http\Controllers\Admin\BackupController;
use Illuminate\Support\Facades\Route;

// Route::get('/backup-database', 'BackupController@backup')->name('backup.database');
Route::get('/backup-database', [BackupController::class,'backupDatabase'])->name('backup.database');

?>


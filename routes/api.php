<?php

use App\Http\Controllers\Api\Admin\CacheCommandController;
use App\Http\Controllers\Api\Common\FileUploader;
use App\Http\Controllers\Api\Dealer\AuthController;
use App\Http\Controllers\Api\Dealer\DealerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth:admin')->get('/user/admin', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:admin')->get('/admin/user', function (Request $request) {
    return $request->user();
});

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return response()->json(['message' => 'Email verified successfully']);
})->middleware(['auth:sanctum', 'signed'])->name('verification.verify');


Route::post('blog/upload',[FileUploader::class, 'blogUpload']);
Route::post('setting/upload',[FileUploader::class, 'settingUpload']);
Route::post('banner/upload',[FileUploader::class, 'bannerUpload']);

Route::get('/dealer', [DealerController::class, 'index']);
Route::post('/dealer', [DealerController::class, 'store']);
Route::get('/dealer/{id}', [DealerController::class, 'show']);
Route::put('/dealer/{id}', [DealerController::class, 'update']);
Route::delete('/dealer/{id}', [DealerController::class, 'destroy']);


Route::post('/dealer/register',[AuthController::class, 'register']);
Route::post('/dealer/login',[AuthController::class, 'login']);
Route::post('/dealer/logout',[AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/admin/register',[App\Http\Controllers\Api\Admin\AuthController::class, 'register']);
Route::post('/admin/login',[App\Http\Controllers\Api\Admin\AuthController::class, 'login']);
Route::post('/admin/logout',[App\Http\Controllers\Api\Admin\AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/dashboard', [App\Http\Controllers\Api\Admin\InventoryController::class, 'dashboard'])->middleware('auth:admin');
Route::get('/inventory', [App\Http\Controllers\Api\Admin\InventoryController::class, 'index'])->middleware('auth:admin');
Route::get('/blogs/{id}', [App\Http\Controllers\Api\Admin\BlogController::class, 'index'])->middleware('auth:admin');
Route::put('/blogs/{id}/status', [App\Http\Controllers\Api\Admin\BlogController::class, 'updateStatus'])->middleware('auth:admin');

Route::post('cache-commands/run-all', [CacheCommandController::class, 'runAll']);
Route::delete('cache-commands/delete-all', [CacheCommandController::class, 'deleteAll']);
Route::post('cache-commands/{id}/run', [CacheCommandController::class, 'runSingle']);
Route::post('cache-commands/{id}/delete-cache', [CacheCommandController::class, 'deleteCache']);

// Route::apiResource('dealers', DealerController::class);


    // Route::get('cache-commands/show', [CacheCommandController::class, 'singleCacheCommands'])->name('single.cache.view');

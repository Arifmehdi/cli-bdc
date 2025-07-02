<?php

use App\Http\Controllers\Admin\AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogSubCategoryController;
use App\Http\Controllers\Admin\CacheCommandController;
use App\Http\Controllers\Admin\GeneralSettingController;
use App\Http\Controllers\Admin\ResearchManageController;
use App\Http\Controllers\Admin\RolesController;

use Illuminate\Support\Facades\Route;


Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::prefix('blogs')->name('blogs.')->controller(AdminBlogController::class)->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('create', 'create')->name('create');
        Route::post('update', 'update')->name('update');
        Route::post('delete', 'delete')->name('delete');
        Route::post('status/change', 'status')->name('status.change');
    });

    Route::prefix('research')->name('research.')->controller(AdminBlogController::class)->group(function(){
        Route::get('auto-news', 'researchAutoNews')->name('auto.news');
        Route::get('reviews', 'researchReviews')->name('reviews');
        Route::get('tools-advice', 'researchToolsAdvice')->name('tools.advice');
        Route::get('car-buying-advice', 'researchCarBuyingAdvice')->name('car.buying.advice');
        Route::get('car-tips', 'researchCartips')->name('car.tips');

    });

    Route::prefix('beyond-car')->name('beyond.car.')->controller(AdminBlogController::class)->group(function(){
        Route::get('news', 'beyondcarNews')->name('news');
        Route::get('innovation', 'beyondcarInnovation')->name('innovation');
        Route::get('opinion', 'beyondcarOpinion')->name('opinion');
        Route::get('financial', 'beyondcarFinancial')->name('financial');

    });


    // blog route
    Route::prefix('blog')->name('blog.')->controller(BlogController::class)->group(function(){
        Route::get('/', 'index')->name('index');
        Route::post('add', 'add')->name('add');
        Route::post('delete', 'delete')->name('delete');
        Route::post('update', 'update')->name('update');
        Route::post('status/change', 'status')->name('status.change');

        Route::resource('category',BlogCategoryController::class)->names('category');
        Route::post('category/update/data', [BlogCategoryController::class, 'category_update'])->name('category.update.v2');

        Route::resource('subcategory',BlogSubCategoryController::class)->names('subcategory');
        Route::post('subcategory/update/data', [BlogSubCategoryController::class, 'subcategory_update'])->name('subcategory.update.v2');

    });
    // Route::get('single/blog/show', [BlogController::class, 'singleNews'])->name('single.blog.view');

    Route::prefix('research')->name('research.')->group(function () {
        Route::resource('news', ResearchManageController::class)->names('news');
    });


    // roles route
    // general setting
    Route::prefix('settings')->group(function(){
        Route::resource('roles', RolesController::class);
        Route::prefix('general')->name('setting.')->controller(GeneralSettingController::class)->group(function(){
            // Route::get('/', [GeneralSettingController::class, 'index'])->name('general');
            Route::get('/', [GeneralSettingController::class, 'index'])->name('index');
            Route::get('image/get', [GeneralSettingController::class, 'identify'])->name('image.get');
            Route::post('add', [GeneralSettingController::class, 'update'])->name('update');
        });

        // permission route
        Route::prefix('permission')->name('permission.')->controller(RolesController::class)->group(function(){
            Route::get('/', [RolesController::class, 'permissionList'])->name('index');
            Route::post('/store', [RolesController::class, 'permissionStore'])->name('store');
            Route::post('/update', [RolesController::class, 'permissionUpdate'])->name('update');
            Route::post('/destroy/{id}', [RolesController::class, 'permissionDelete'])->name('destroy');
        });
    });

    Route::get('cache-commands', [CacheCommandController::class, 'index'])->name('cache-commands.index');
    Route::post('cache-commands/delete-all', [CacheCommandController::class, 'deleteAll'])->name('cache-commands.delete-all');
    Route::post('cache-commands/run-all', [CacheCommandController::class, 'runAll'])->name('cache-commands.run-all');
    Route::post('cache-commands/{id}/run', [CacheCommandController::class, 'runSingle'])->name('cache-commands.run');
    Route::post('cache-commands/{id}/delete-cache', [CacheCommandController::class, 'deleteCache'])->name('cache-commands.delete-cache');
    Route::get('cache-commands/show', [CacheCommandController::class, 'singleCacheCommands'])->name('single.cache.view');

});


?>


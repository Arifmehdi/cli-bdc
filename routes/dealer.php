<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\Dealer\DealerController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Dealer\DealerLeadController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;


// change when add differnt table start here
Route::prefix('dealer')->name('dealer.')->middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
});
// change when add differnt table start here


Route::prefix('dealer')->name('dealer.')->middleware('auth')->group(function () {



    Route::get('/dashboard', [ApplicationController::class, 'index'])->name('dashboard');

    Route::get('/profile', [DealerController::class, 'index'])->name('profile');
    Route::post('/profile/update', [DealerController::class, 'profileUpdate'])->name('profile.update');
    // lead show
    Route::get('/leads/show', [DealerLeadController::class, 'leadShow'])->name('lead.show');
    Route::post('/purchases', [DealerLeadController::class, 'purchase'])->name('purchases');
    Route::get('single/lead/view/{id}', [LeadController::class, 'singleLeadShow'])->name('single.lead.view');
    Route::post('single/lead/delete', [LeadController::class, 'deleteLead'])->name('single.lead.delete');
    Route::post('/lead/bulk-actions', [LeadController::class, 'bulkAction'])->name('lead.bulk-action');
    Route::get('/lead/restore/{id}', [LeadController::class, 'restore'])->name('lead.restore');
    Route::delete('/lead/permanent/delete/{id}', [LeadController::class, 'permanentDelete'])->name('lead.permanent.delete');
    // dealer add to cart lead route
    Route::post('/add/to-cart', [DealerLeadController::class, 'addCart'])->name('add.tocart');

    // dealer invoice show route
    Route::get('/invoice/show', [DealerLeadController::class, 'invoiceshow'])->name('invoice.show');

});

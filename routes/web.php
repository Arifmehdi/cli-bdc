<?php

use App\Models\Faq;
// use App\Http\Controllers\RolesController;
// use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\admin\GeneralSettingController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminDealerManagement;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FTPController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\TipsController;
use App\Http\Controllers\DealershipController;
use App\Http\Controllers\NewsReviewController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Admin\AprRateController;
use App\Http\Controllers\Admin\CompareController;
use App\Http\Controllers\Admin\GeneralController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\TendingController;
use App\Http\Controllers\BeyondCarBlogController;
use App\Http\Controllers\Dealer\DealerController;
use App\Http\Controllers\Admin\AdminLogoController;
use App\Http\Controllers\ResearchCarBlogController;
use App\Http\Controllers\Admin\MembershipController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Admin\AdminFooterController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\FileManagerController;
use App\Http\Controllers\Admin\LatestVideoController;
use App\Http\Controllers\Admin\VehicleBodyController;
use App\Http\Controllers\Admin\VehicleMakeController;
use App\Http\Controllers\Admin\VehicleTrimController;
use App\Http\Controllers\Admin\VehicleYearController;
use App\Http\Controllers\Admin\DealerImportController;
use App\Http\Controllers\Admin\LocationZipsController;
use App\Http\Controllers\Admin\VehicleModelController;
use App\Http\Controllers\Admin\AdminFrontendController;
use App\Http\Controllers\Admin\AdvertisementController;
use App\Http\Controllers\Admin\CSVManagementController;
use App\Http\Controllers\Admin\LocationStateController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\FileManagementController;
use App\Http\Controllers\Admin\LocationCitiesController;
use App\Http\Controllers\Auth\CustomCheckAuthController;
use App\Http\Controllers\Admin\InventoryImportController;
use App\Http\Controllers\Admin\RequestInventoryController;
use App\Http\Controllers\Admin\AdminDealerMessageController;
use App\Http\Controllers\Admin\LocationBulkImportController;
use App\Http\Controllers\Admin\AdminTermsConditionController;
use App\Http\Controllers\Admin\AdminDealerManagementController;
use App\Http\Controllers\Admin\InventoryUpdateImportController;
use App\Http\Controllers\ListingDetailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


require __DIR__ . '/auth.php';
require __DIR__ . '/dealer.php';
require __DIR__ . '/custom.php';
require __DIR__ . '/admin.php';

Route::get('/not-found', function () {
    return view('errors_view.page_404');
})->name('not-found');

Route::get('/car-not-found', function () {
    return view('errors_view.car_not_find');
})->name('car-not-found');

Route::post('cars-for-sale/buyer/listing/store', [SiteController::class, 'listing_add'])->name('request.inventory.store');
Route::post('/change/password', [DealerController::class, 'changePassword'])->name('change-password');
Route::get('/zip-code', [SiteController::class, 'zip'])->name('zip');
Route::get('/', [SiteController::class, 'index'])->name('home');
Route::get('/get-location', [SiteController::class, 'getLocation'])->name('locationSet');
// Route::get('/best-used-cars-for-sale/listing/{param?}', [SiteController::class, 'auto'])->name('auto');
Route::get('/best-used-cars-for-sale/listing/{param?}', [ListingController::class, 'auto'])->name('auto');
Route::get('/best-new-cars-for-sale/listing/{param?}', [SiteController::class, 'newAuto'])->name('auto.new');
Route::get('/arp/ajax', [SiteController::class, 'arpRateByAjax'])->name('arp.ajax');
Route::get('/arp/ajax-value', [SiteController::class, 'arpRateValue'])->name('arp.ajax.value');
// Route::get('/best-new-cars-for-sale/new/listing/{param?}',[SiteController::class,'auto'])->name('new.auto');

// Route::get('/best-dealer/listing/{param?}',[SiteController::class,'allDealerListing'])->name('dealer.listing');
Route::get('/dealer-cars/{stockId?}/{dealer_name?}/{id?}', [DealershipController::class, 'dealerinfo'])->name('dealer');
// Route::post('/dealer-cars/{stockId?}/{dealer_name?}/{id?}', [DealershipController::class, 'dealerinfo'])->name('dealer');
// Route::get('/best-used-cars-for-sale/listing/{vin?}/{param?}', [SiteController::class, 'autoDetails'])->name('auto.details');
Route::get('/best-used-cars-for-sale/listing/{vin?}/{param?}', [ListingDetailController::class, 'autoDetails'])->name('auto.details');

Route::get('/auto-ajax', [SiteController::class, 'autoAjax'])->name('auto.ajax');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
// Route::get('/research', [SiteController::class, 'research'])->name('research');
// Route::get('/articles/{slug?}', [SiteController::class, 'article_details'])->name('article.details');
Route::get('/cars/forsale', [SiteController::class, 'forsale'])->name('car.used');
Route::get('/cars/new', [SiteController::class, 'forSaleNew'])->name('car.new');
Route::get('/quick/view/{id?}', [SiteController::class, 'quick'])->name('quick.show');

// web.php
Route::get('/refresh-captcha', [CaptchaController::class, 'refreshCaptcha']);


Route::get('/payment/success', [SiteController::class, 'success_message'])->name('success');
Route::get('/payment/fail', [SiteController::class, 'fail_message'])->name('fail');

Route::get('/get-ip', [SiteController::class, 'ipAddress'])->name('get.ipaddress');

Route::get('/search', [SiteController::class, 'search'])->name('search');

Route::group(['middleware' => ['web']], function () {
    Route::post('/contact/message', [SiteController::class, 'contact_message'])->name('contact.store');
});


Route::get('/homePage/modelSearch/{id?}', [SiteController::class, 'modelSearch'])->name('homePage.modelSearch');
Route::post('/homePage/bodySearch', [SiteController::class, 'bodySearch'])->name('homePage.bodySearch');



// lead send
Route::post('/lead/send', [LeadController::class, 'lead'])->name('lead.send');
Route::get('/lead/info', [LeadController::class, 'lead_info'])->name('get_lead_data');


// create account route
Route::post('/create/account', [CustomCheckAuthController::class, 'store'])->name('create.account');

// check email route
Route::post('/email/check', [CustomCheckAuthController::class, 'checkMail'])->name('check.email');
// login route
Route::post('/buyer/login', [CustomCheckAuthController::class, 'login'])->name('buyer.login');
Route::get('register/verify/{id}/{password?}', [CustomCheckAuthController::class, 'userVerify'])->name('verify.user');
Route::get('/buyer/logout', [CustomCheckAuthController::class, 'logout'])->name('buyer.logout');


// forgot password

Route::post('/forget/password', [CustomCheckAuthController::class, 'forgot_email'])->name('forgot.password.email');
Route::post('/check/otp', [CustomCheckAuthController::class, 'checkOtp'])->name('check.otp.email');

// wishlist update route

Route::post('/update/wishlist', [InventoryController::class, 'updateWishList'])->name('update.wishlist');
Route::get('/favorite/listing', [SiteController::class, 'favourite'])->name('favourite.listing');

// google login route
Route::get('/google/login', [CustomCheckAuthController::class, 'googleLogin'])->name('google.login');
Route::get('/auth/google/callback', [CustomCheckAuthController::class, 'googleHandle']);
// facebook login route
Route::get('/facebook/login', [CustomCheckAuthController::class, 'facebookLogin'])->name('facebook.login');
Route::get('/facebook/callback', [CustomCheckAuthController::class, 'facebookHandle']);

// frontend route

Route::name('frontend.')->group(function () {
    // refactor later
    Route::post('state/search/{id?}', [GeneralSettingController::class, 'state_search'])->name('state.search');


    Route::get('cars/find-dealership/{page?}', [DealershipController::class, 'ShowfindDealerShip'])->name('find.dealership');

    Route::get('/research-review/{categorySlug}/{slug}', [ResearchCarBlogController::class, 'reseacrhReviewArticleDetails'])->name('research.review.autonews.details');
    Route::get('/beyond-car/{categorySlug}/{slug}', [ResearchCarBlogController::class, 'reseacrhReviewArticleDetails'])->name('research.beyondcar.article.details');

    Route::get('/research-review', [ResearchCarBlogController::class, 'research'])->name('research.review');
    Route::get('/research-review/auto-news', [ResearchCarBlogController::class, 'autonews'])->name('research.auto.news');
    Route::get('/research-review/review', [ResearchCarBlogController::class, 'autoreview'])->name('research.auto.review');
    Route::get('/research-review/tools-and-advice', [ResearchCarBlogController::class, 'researchToolsAndAdvice'])->name('research.tools.advice');
    Route::get('/research-review/car-buying-advice', [ResearchCarBlogController::class, 'researchCarBuyingAdvice'])->name('research.car.buying.advice');
    Route::get('/research-review/car-tips', [ResearchCarBlogController::class, 'researchCarTips'])->name('research.car.tips');
    Route::get('/research-review/videos', [ResearchCarBlogController::class, 'researchVideos'])->name('research.videos');
    Route::get('/research-review/faq', [ResearchCarBlogController::class, 'faq'])->name('faq');


    // delete after check
    Route::get('/research-review/all-research', [NewsReviewController::class, 'researchAll'])->name('research.researchall');
    Route::get('/research-review/reviews', [NewsReviewController::class, 'researchViews'])->name('research.reviews');
    // Route::get('/research-review/tools-&-expert-advice', [NewsReviewController::class, 'researchToolsAndExpertAdvice'])->name('research.toolsexpertadvice');
    // Route::get('/research-review/videos', [NewsReviewController::class, 'researchVideos'])->name('research.videos');
    // Route::get('/research-review/latest-car-buying-advice', [NewsReviewController::class, 'researchLatestCarBuyingDevice'])->name('research.latestcarbuyingadvice');

    Route::get('/beyond-car/tools-and-advice', [NewsReviewController::class, 'beyondCartoolsAndAdvice'])->name('beyondcar.toolsandadvice');

    Route::get('/beyond-car', [BeyondCarBlogController::class, 'beyondCar'])->name('beyondcar');
    Route::get('/beyond-car/news', [BeyondCarBlogController::class, 'beyondCarNews'])->name('beyondcar.news');
    Route::get('/beyond-car/innovation', [BeyondCarBlogController::class, 'beyondCarInnovation'])->name('beyondcar.innovation');
    Route::get('/beyond-car/opinion', [BeyondCarBlogController::class, 'beyondCarOpinion'])->name('beyondcar.opinion');
    Route::get('/beyond-car/financial', [BeyondCarBlogController::class, 'beyondCarFinancial'])->name('beyondcar.financial');

    Route::get('/about-us', [SiteController::class, 'about'])->name('about.us');
    Route::get('/auto-news/{slug}', [SiteController::class, 'details'])->name('news.details');
    Route::get('/article/{slug}', [SiteController::class, 'articles'])->name('articles');
    Route::get('/review/{slug}', [SiteController::class, 'reviews'])->name('reviews');
    Route::get('/articles{id?}/{title?}', [SiteController::class, 'Rdetails'])->name('review.details');
    Route::get('/tips', [SiteController::class, 'tips'])->name('tips.page');
    Route::get('/tips/{slug}', [SiteController::class, 'tipsDetails'])->name('tips.details');
    // Route::get('/tips{id?}', [SiteController::class, 'tipsdetails'])->name('tips.details');

    Route::get('/auto-news', [SiteController::class, 'news'])->name('news.page');

    Route::get('/vedio/details/{id}', [SiteController::class, 'vedioDetails'])->name('vedio.page.details');
    Route::get('/terms-condition', [SiteController::class, 'termsCondition'])->name('terms.condition');
    Route::get('/privacy-policy', [SiteController::class, 'privacyPolicy'])->name('privacy.policy');
    Route::get('/setup-password/{id}', [SiteController::class, 'setupPassword'])->name('setup.password');
    Route::post('/setup/new-password/{id}', [SiteController::class, 'login'])->name('setup_new_buyer.login');
    Route::post('/subscribe/user', [SiteController::class, 'subscribe'])->name('subscribe.user');

    // Share Email
    Route::post('share/mail', [LeadController::class, 'share'])->name('share.email');


    Route::post('/listing/compare/add', [CompareController::class, 'add'])->name('compare.listing');
    Route::get('/compare/listing', [CompareController::class, 'index'])->name('compare.show');
    Route::post('/listing/compare', [CompareController::class, 'collect'])->name('compare.listing.collect');
    // In routes/web.php or routes/api.php
    Route::delete('/delete-item/{id}', [CompareController::class, 'deleteItem']);
});


// Admin to dealer messages route
Route::group(['prefix' => 'messages'], function () {
    Route::post('/users', [AdminDealerMessageController::class, 'getUser'])->name('messages.users');
    Route::get('/', [AdminDealerMessageController::class, 'index'])->name('messages');
    Route::get('create', [AdminDealerMessageController::class, 'create'])->name('messages.create');
    Route::post('/', [AdminDealerMessageController::class, 'store'])->name('messages.store');
    Route::get('{id}', [AdminDealerMessageController::class, 'show'])->name('messages.show');
    Route::put('{id}', [AdminDealerMessageController::class, 'update'])->name('messages.update');
});


// Auth::routes(['register' => false]);
// admin dashboard route


Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/dashboard', [ApplicationController::class, 'index'])->name('dashboard');

    Route::get('/request/inventories', [RequestInventoryController::class, 'index'])->name('req.inventory');
    Route::post('/request/inventories/status', [RequestInventoryController::class, 'status'])->name('req.change.status');

    Route::post('/ckeditor/image', [UploadController::class, 'uploadImage'])->name('ckeditor.upload');
    // dealer import route
    Route::get('/dealer/import', [DealerImportController::class, 'index'])->name('dealer.import');
    Route::post('/dealer/inventory-store', [DealerImportController::class, 'storeDealer'])->name('dealer.store');
    Route::post('/dealer/dealer-csv-store', [DealerImportController::class, 'storeCSVDealer'])->name('dealer.csv.store');
    Route::delete('/dealer/delete/{id}', [DealerImportController::class, 'destroy'])->name('dealer.delete');

    Route::get('/location/import', [LocationBulkImportController::class,'index'])->name('bulk.import');
    // Route::post('/dealer/inventory-store', [DealerImportController::class, 'storeDealer'])->name('dealer.store');
    Route::post('/location/location-csv-store', [LocationBulkImportController::class, 'storeCSVLocation'])->name('location.csv.store');
    // Route::get('/location/import', [LocationBulkImportController::class,'bulkImport '])->name('bulk.import');

    Route::get('/inventory/import', [InventoryImportController::class, 'index'])->name('inventory.import');
    Route::get('/inventory/v1', [InventoryImportController::class, 'inventoryImportRequest'])->name('inventory.import.v1');
    Route::get('/inventory/update/v1', [InventoryUpdateImportController::class, 'inventoryUpdateRequest'])->name('inventory.update.v1');
    Route::post('/inventory/update/csv-store', [InventoryUpdateImportController::class, 'storeCSVUpdateInventory'])->name('inventory.update.csv');

    Route::get('/inventory/view/v1/{id?}', [InventoryImportController::class, 'inventoryRequestView'])->name('inventory.list.v1');
    Route::get('/inventory/view/v1/delete/{id?}', [InventoryImportController::class, 'inventoryRequestDelete'])->name('inventory.list.delete.v1');

    Route::post('/user/vehicle-store', [InventoryImportController::class, 'storeInventory'])->name('inventory.store');
    Route::post('/user/vehicle-store/v1', [InventoryImportController::class, 'storeInventoryv1'])->name('inventory.store.v1');
    Route::post('/user/vehicle-csv-store', [InventoryImportController::class, 'storeCSVInventory'])->name('inventory.csv.store');

    Route::get('/inventory/list', [AdminInventoryController::class, 'index'])->name('inventory.list');
    Route::get('/inventory/sold', [AdminInventoryController::class, 'soldInventory'])->name('inventory.sold');
    Route::get('/inventory/update', [AdminInventoryController::class, 'updateInventory'])->name('inventory.update');
    Route::get('/no-img/inventory/list', [AdminInventoryController::class, 'noImageIndex'])->name('noimage.inventory.list');
    Route::get('/active/inventory/list-DP', [AdminInventoryController::class, 'activeInventoryListDP'])->name('active.inventory.list.dp');
    Route::post('/inventory/delete', [AdminInventoryController::class, 'destroy'])->name('inventory.delete');
    Route::post('/inventory/bulk-actions', [AdminInventoryController::class, 'bulkAction'])->name('inventory.bulk-action');
    Route::get('/inventory/restore/{id}', [AdminInventoryController::class, 'restore'])->name('inventory.restore');
    Route::delete('/inventory/permanent/delete/{id}', [AdminInventoryController::class, 'permanentDelete'])->name('inventory.permanent.delete');

    // dealer package update route
    Route::get('/dealer/management/ajax', [AdminDealerManagementController::class, 'dealarManageAjax'])->name('dealer.management.ajax');
    Route::post('/dealer/membership/add-cart', [AdminDealerManagementController::class, 'addtoCartMembership'])->name('dealer.membership.add.cart');

    Route::post('cars-for-sale/buyer/listing/save', [SiteController::class, 'listing_save'])->name('inventory.store.save');

    // Route::post('ftp/listing/save', [SiteController::class, 'listing_save'])->name('inventory.store.save');

    // Route::post('/ftp/process-file', [SiteController::class, 'processFile'])->name('ftp.process-file');
    Route::get('/ftp/progress', [SiteController::class, 'getProgress'])->name('ftp.progress');
    Route::post('/settings', [FTPController::class, 'updateSettings'])->name('update.ftp.settings');
    Route::post('/csv/filemanager', [FTPController::class, 'csvFileManager'])->name('update.csv.manager');
    Route::post('/download-file-delete/{filename}', [FTPController::class, 'csvFileDelete'])->name('csv.delete');
    // cart item
    Route::get('/cart/item', [CartController::class, 'getcartItem'])->name('get.cart_item');
    Route::post('/cart/delete-all', [CartController::class, 'deleteAllCartItem'])->name('cart.deleteAll');
    Route::post('/cart/delete', [CartController::class, 'deleteCartItem'])->name('cart.data.delete');
    // card
    Route::get('/card/payment', [CardController::class, 'index'])->name('card.payment');

    // invoice store
    Route::post('/invoice/store', [InvoiceController::class, 'invoiceStore'])->name('invoice.store');
    Route::post('/invoice/new/store', [InvoiceController::class, 'invoiceNewStore'])->name('invoice.new.store');
    Route::post('invoice/show', [InvoiceController::class, 'show'])->name('cart.invoice.show');
    Route::post('invoice/delete', [InvoiceController::class, 'invoiceDelete'])->name('invoice.delete');
    Route::get('/invoice/show', [InvoiceController::class, 'InvoiceShow'])->name('invoice.show');
    Route::post('/invoice/restore/{id}', [InvoiceController::class, 'restore'])->name('invoice.restore');
    Route::delete('/invoice/permanent/delete/{id}', [InvoiceController::class, 'permanentDelete'])->name('invoice.permanent.delete');
    Route::get('/dealer/invoice/{id?}', [InvoiceController::class, 'allInvoice'])->name('invoice.list');
    Route::get('/invoice/pdf/{id?}', [InvoiceController::class, 'invoicePdf'])->name('invoice.pdf');
    Route::post('/invoice/change/status', [InvoiceController::class, 'changeStatus'])->name('invoice.change_status');
    Route::get('/dealer/download/membership-invoice/{id}', [InvoiceController::class, 'membershipInvoiceDownload'])->name('dealer.download.membership.invoice');
    Route::post('/single/invoice/change-status', [InvoiceController::class, 'membershipInvoiceStatusChange'])->name('single.invoice.change.status');
    //  membership route
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership');
    Route::post('/membership/add', [MembershipController::class, 'add'])->name('membership.add');
    Route::post('/membership/update', [MembershipController::class, 'update'])->name('membership.update');
    Route::post('/membership/delete', [MembershipController::class, 'delete'])->name('membership.delete');
    Route::post('/membership/change/status', [MembershipController::class, 'change'])->name('membership.status.change');
    // lead show
    Route::get('/leads/show/{id?}', [LeadController::class, 'leadShow'])->name('lead.show');
    Route::get('/lead/seen', [LeadController::class, 'leadSeen'])->name('lead.seen');
    Route::get('single/lead/view/{id}', [LeadController::class, 'singleLeadShow'])->name('single.lead.view');
    Route::post('single/lead/delete', [LeadController::class, 'deleteLead'])->name('single.lead.delete');
    Route::post('/lead/bulk-actions', [LeadController::class, 'bulkAction'])->name('lead.bulk-action');
    Route::get('/lead/restore/{id}', [LeadController::class, 'restore'])->name('lead.restore');
    Route::delete('/lead/permanent/delete/{id}', [LeadController::class, 'permanentDelete'])->name('lead.permanent.delete');


    Route::get('/vichele/dealer/lead', [LeadController::class, 'searchVichele'])->name('vichele.dealer.lead');
    Route::post('/select/car',[LeadController::class,'selectCar'])->name('select.car');
    Route::post('/lead/store', [LeadController::class, 'adminLeadStore'])->name('lead.store');

    Route::post('/invoice/bulk-actions', [InvoiceController::class, 'bulkAction'])->name('invoice.bulk-action');

    // dealer lead send
    Route::post('dealer/email/send', [LeadController::class, 'emailSend'])->name('dealer.email.send');
    Route::post('dealer/adf/email/send', [LeadController::class, 'adfemailSend'])->name('dealer.adf.email.send');
    // inventory
    Route::get('/inventory/edit/{id?}', [AdminInventoryController::class, 'editPage'])->name('inventory.edit.page');
    Route::get('/inventory/image/edit/{id?}', [AdminInventoryController::class, 'imgEditPage'])->name('image.edit');
    Route::post('admin/inventory/edit', [AdminInventoryController::class, 'edit'])->name('update.inventory');

    // Dealer Profile route for admin dashboard
    Route::get('/dealer/profile/{id}', [AdminDealerManagementController::class, 'profileDetails'])->name('dealer.profile');
    Route::get('/dealer/lead/show/{id}', [AdminDealerManagementController::class, 'dealerLeadShow'])->name('dealer.lead.show');
    Route::get('/dealer/invoice/show/{id}', [AdminDealerManagementController::class, 'dealerInvoiceShow'])->name('dealer.invoice.show');
    Route::post('/single/invoice/delete', [AdminDealerManagementController::class, 'deleteInvoice'])->name('single.invoice.delete');


    //  admin dealer management route



});

Route::prefix('dealer')->name('dealer.')->group(function () {

    Route::get('/manage', [AdminDealerManagementController::class, 'index'])->name('manage');
    Route::post('/store', [AdminDealerManagementController::class, 'store'])->name('store');
    Route::post('/update', [AdminDealerManagementController::class, 'update'])->name('update');
    Route::post('/delete', [AdminDealerManagementController::class, 'delete'])->name('delete');
    Route::post('/change-status', [AdminDealerManagementController::class, 'changeStatus'])->name('change-status');
});


Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {


    // // Route::post('/admin/file-management/upload', [FileManagementController::class, 'upload'])->name('file-management.upload');
    Route::prefix('file-management')->controller(FileManagementController::class)->group(function(){
        // Route::view('/','FileManagement.Index');
        // Default route names will be prefixed with admin.
        Route::get('filemanager','fileManagerIndex')->name('filemanager.index');
        Route::get('/','index')->name('file-management.index');
        Route::post('extract', 'extract')->name('file-management.extract');
        Route::get('get-all','getAllFilesAndFolders')->name('file-management.getAllFilesAndFolders');
        Route::post('create-file','createFile')->name('file-management.createFile');
        Route::post('create-folder','createFolder')->name('file-management.createFolder');
        Route::post('rename','rename')->name('file-management.rename');
        Route::post('paste','paste')->name('file-management.paste');
        Route::post('zip-folder','zipFolder')->name('file-management.zipFolder');
        Route::post('delete','delete')->name('file-management.delete');
        Route::get('download','download')->name('file-management.download');
        Route::post('upload', 'upload')->name('file-management.upload');
        Route::get('/upload','upload')->name('file-management.upload');

    });

    Route::prefix('file-manager')->controller(FileManagerController::class)->group(function(){
        // Route::view('/','FileManagement.Index');
        Route::get('/','index')->name('file-manager.index');
        Route::get('get-all','getAllFilesAndFolders')->name('file-manager.getAllFilesAndFolders');
        Route::post('create-file','createFile')->name('file-manager.createFile');
        Route::post('create-folder','createFolder')->name('file-manager.createFolder');
        Route::post('rename','rename')->name('file-manager.rename');
        Route::post('paste','paste')->name('file-manager.paste');
        Route::post('zip-folder','zipFolder')->name('file-manager.zipFolder');
        Route::post('delete','delete')->name('file-manager.delete');
        Route::get('download','download')->name('file-manager.download');
        Route::post('/upload','upload')->name('file-manager.upload');
        Route::post('extract', 'extract')->name('file-manager.extract');

    });

    Route::prefix('csv-management')->controller(CSVManagementController::class)->group(function(){
        // Route::view('/','csvManagement.Index');
        // Default route names will be prefixed with admin.
        Route::get('/','index')->name('csv-management.index');
        Route::post('extract', 'extract')->name('csv-management.extract');
        Route::get('get-all','getAllcsvsAndFolders')->name('csv-management.getAllcsvsAndFolders');
        Route::post('create-csv','createcsv')->name('csv-management.createcsv');
        Route::post('create-folder','createFolder')->name('csv-management.createFolder');
        Route::post('rename','rename')->name('csv-management.rename');
        Route::post('paste','paste')->name('csv-management.paste');
        Route::post('zip-folder','zipFolder')->name('csv-management.zipFolder');
        Route::post('delete','delete')->name('csv-management.delete');
        Route::get('download','download')->name('csv-management.download');
        Route::post('upload', 'upload')->name('csv-management.upload');
        // Route::get('/upload','upload')->name('csv-management.upload');

    });

    Route::get('/profile', [DealerController::class, 'index'])->name('profile');
    Route::get('/users', [ApplicationController::class, 'user'])->name('users');
    Route::post('/change/user/status', [ApplicationController::class, 'changeUserStatus'])->name('change.user.status');
    Route::post('/user/store', [ApplicationController::class, 'store'])->name('users.store');
    Route::post('/user/update', [ApplicationController::class, 'update'])->name('user.update');
    Route::post('/user/view', [ApplicationController::class, 'upp'])->name('user.view');
    Route::post('/user/delete', [ApplicationController::class, 'delete'])->name('user.delete');

    // news route
    Route::get('/news/show', [NewsController::class, 'show'])->name('news.show');
    Route::get('single/news/show', [NewsController::class, 'singleNews'])->name('single.news.view');
    Route::post('/news/add', [NewsController::class, 'add'])->name('news.add');
    Route::post('/news/delete', [NewsController::class, 'delete'])->name('single.news.delete');
    Route::post('/news/update', [NewsController::class, 'update'])->name('news.update');
    Route::post('/news/status/change', [NewsController::class, 'status'])->name('news.status.change');

    // review route
    Route::get('/review/show', [ReviewController::class, 'show'])->name('review.show');
    Route::post('/review/add', [ReviewController::class, 'add'])->name('review.add');
    Route::post('/review/delete', [ReviewController::class, 'delete'])->name('review.delete');
    Route::post('/review/update', [ReviewController::class, 'update'])->name('review.update');
    Route::post('/review/status/change', [ReviewController::class, 'status'])->name('review.status.change');

    // tips route
    Route::get('/tips/show', [TipsController::class, 'show'])->name('tips.show');
    Route::post('/tips/add', [TipsController::class, 'add'])->name('tips.add');
    Route::post('/tips/delete', [TipsController::class, 'delete'])->name('tips.delete');
    Route::post('/tips/update', [TipsController::class, 'update'])->name('tips.update');
    Route::post('/tips/status/change', [TipsController::class, 'status'])->name('tips.status.change');


    //  faq route
    Route::get('/faqs/show', [FaqController::class, 'faq_show'])->name('faq.show');
    Route::post('/faq/add', [FaqController::class, 'add'])->name('faq.add');
    Route::post('/faq/delete', [FaqController::class, 'delete'])->name('faq.delete');
    Route::post('/faq/update', [FaqController::class, 'update'])->name('faq.update');
    Route::post('/faq/change/status', [FaqController::class, 'changeStatus'])->name('faqs.status.change');

    // advertisement route
    Route::get('/add/show', [AdvertisementController::class, 'add_show'])->name('advertisement.show');
    Route::post('/add/add', [AdvertisementController::class, 'add'])->name('ad.add');
    Route::post('/add/delete', [AdvertisementController::class, 'delete'])->name('ad.delete');
    Route::post('/add/update', [AdvertisementController::class, 'update'])->name('ad.update');
    Route::post('/add/change/status', [AdvertisementController::class, 'changeStatus'])->name('ad.status.change');

    // subscriber route
    Route::get('/subscriber/show', [ApplicationController::class, 'subscriber_show'])->name('subscriber.show');
    Route::post('/subscriber/delete', [ApplicationController::class, 'sub_delete'])->name('subscriber.delete');
    Route::post('/subscriber/status/change', [ApplicationController::class, 'statusChange'])->name('sub.status.change');


    // banner route
    Route::get('/banner/show', [BannerController::class, 'banner_show'])->name('banner.show');
    Route::post('/banner/add', [BannerController::class, 'add'])->name('banner.add');
    Route::post('/banner/edit', [BannerController::class, 'edit'])->name('banner.edit');
    Route::post('/banner/status/change', [BannerController::class, 'changeActiveInactive'])->name('banner.change.status');


    // link route
    Route::get('/link/create', [LinkController::class, 'create'])->name('links.show');
    Route::post('/link/add', [LinkController::class, 'add'])->name('links.add');
    Route::post('/link/edit', [LinkController::class, 'update'])->name('links.edit');
    Route::post('/links/delete', [LinkController::class, 'delete'])->name('single.links.delete');
    Route::post('/links/status/change', [LinkController::class, 'statusChange'])->name('link.status.change');


    // Tending Route
    Route::get('/tending', [TendingController::class, 'index'])->name('tending.show');
    Route::post('/tending/add', [TendingController::class, 'add'])->name('tending.add');
    Route::post('/tending/edit', [TendingController::class, 'update'])->name('tending.update');
    Route::post('/tending/delete', [TendingController::class, 'delete'])->name('tending.delete');
    Route::post('/tending/status/change', [TendingController::class, 'changeStatus'])->name('tending.status.change');

    // Tending Route
    Route::get('/latest-video', [LatestVideoController::class, 'index'])->name('video.show');
    Route::post('/latest-video/add', [LatestVideoController::class, 'add'])->name('video.add');
    Route::post('/latest-video/edit', [LatestVideoController::class, 'update'])->name('video.update');
    Route::post('/latest-video/delete', [LatestVideoController::class, 'delete'])->name('video.delete');
    Route::post('/latest-video/status/change', [LatestVideoController::class, 'changeStatus'])->name('video.status.change');


    //contact route
    Route::get('/contact/show/{id?}', [LeadController::class, 'contactShow'])->name('contact.show');
    Route::get('single/contact/show', [LeadController::class, 'singleContact'])->name('single.contact.view');
    Route::post('single/contact/delete', [LeadController::class, 'deleteContact'])->name('single.contact.delete');
    Route::post('contact/delete', [LeadController::class, 'deleteContactAll'])->name('contact.delete');
    Route::get('contact/restore/{id}', [LeadController::class, 'contactRestore'])->name('contact.restore');
    Route::delete('contact/permanent/delete/{id}', [LeadController::class, 'contactPermanentDelete'])->name('contact.permanent.delete');

    // message
    Route::get('lead/message/view', [LeadController::class, 'messageShow'])->name('message.view');
    Route::post('lead/reply/message/send', [LeadController::class, 'messageSend'])->name('message.send');
    Route::post('make/search/{id?}', [GeneralController::class, 'searchByid'])->name('make.search');

    // car configaration route
    Route::resource('rates', AprRateController::class);
    Route::resource('years', VehicleYearController::class);
    Route::resource('makes', VehicleMakeController::class);
    Route::resource('models', VehicleModelController::class);
    Route::resource('trims', VehicleTrimController::class);
    Route::resource('body', VehicleBodyController::class);
    Route::resource('states', LocationStateController::class);
    Route::resource('cities', LocationCitiesController::class);
    Route::resource('zips', LocationZipsController::class);
    Route::post('make/update/data', [VehicleMakeController::class, 'make_update'])->name('make.update');
    Route::post('body/update/data', [VehicleBodyController::class, 'body_update'])->name('body.update');
    Route::post('states/update/data', [LocationStateController::class, 'state_update'])->name('state.update');
    Route::post('cities/update/data', [LocationCitiesController::class, 'city_update'])->name('city.update');
    Route::post('zips/update/data', [LocationZipsController::class, 'zip_update'])->name('zip.update');
    // Route::post('body/update/data', [VehicleBodyController::class, 'body_update'])->name('body.update');




    // frontend management route

    // hero slider section
    Route::get('/frontend/slider', [AdminFrontendController::class, 'index'])->name('frontend.slider.index');
    Route::post('/frontend/slider/store', [AdminFrontendController::class, 'store'])->name('frontend.slider.store');
    Route::post('/frontend/slider/update', [AdminFrontendController::class, 'update'])->name('frontend.slider.update');
    Route::post('/frontend/slider/delete', [AdminFrontendController::class, 'delete'])->name('frontend.slider.delete');
    Route::post('/frontend/slider/status-change', [AdminFrontendController::class, 'changeStatus'])->name('slider.status.change');

    // terms condition section
    Route::get('/terms-condition', [AdminTermsConditionController::class, 'index'])->name('terms.condition');
    Route::post('/terms-condition/add', [AdminTermsConditionController::class, 'store'])->name('terms-condition.add');
    Route::post('/terms-condition/update', [AdminTermsConditionController::class, 'update'])->name('terms-condition.update');
    Route::post('/terms-condition/delete', [AdminTermsConditionController::class, 'delete'])->name('terms-condition.delete');


    // header menu section
    Route::get('/frontend/menu', [NavigationController::class, 'index'])->name('frontend.menu.index');
    Route::post('/frontend/menu/store', [NavigationController::class, 'store'])->name('frontend.menu.store');
    Route::post('/frontend/menu/status/change', [NavigationController::class, 'changeStatus'])->name('menu.status.change');
    Route::post('/frontend/menu/priority-change', [NavigationController::class, 'changePriority'])->name('menu.priority-change');
    Route::post('/frontend/menu/delete', [NavigationController::class, 'deleteMenu'])->name('frontend.menu.delete');
    Route::post('/frontend/menu/update', [NavigationController::class, 'updateMenu'])->name('frontend.menu.update');

    // header  logo section

    Route::post('/logo/store', [AdminLogoController::class, 'store'])->name('logo.store');
    Route::post('/logo/update', [AdminLogoController::class, 'update'])->name('frontend.logo.update');
    Route::post('/logo/delete', [AdminLogoController::class, 'delete'])->name('logo.delete');
    Route::post('/logo/status/change', [AdminLogoController::class, 'statusChange'])->name('logo.status.change');

    // user tracking show  route
    Route::get('/user/track-history', [AdminFrontendController::class, 'getUserHistory'])->name('user.track.history');
    Route::post('/user/track-history/delete', [AdminFrontendController::class, 'UserHistoryDelete'])->name('user.track.history.delete');
    // create new page route start
    Route::get('/add/new-page', [PageController::class, 'add'])->name('frontend.add.page');
    Route::post('/check/duplicate/title', [PageController::class, 'checkDuplicateTitle'])->name('check.duplicate.title');
    Route::post('/store/new-page', [PageController::class, 'store'])->name('frontend.page.store');
    Route::get('/all/pages', [PageController::class, 'show'])->name('frontend.all.page');
    Route::get('/edit/page/{id}', [PageController::class, 'edit'])->name('frontend.edit.page');
    Route::post('/update/page/{id}', [PageController::class, 'update'])->name('frontend.update.page');
    Route::post('/delete/page', [PageController::class, 'delete'])->name('frontend.page.delete');
    Route::post('/status/change/page', [PageController::class, 'status'])->name('page.status.change');


    //create static page rooute here
    Route::get('/show/static-page', [PageController::class, 'showStaticPage'])->name('frontend.show.static.page');
    Route::post('/update/static-page', [PageController::class, 'updateStaticPage'])->name('fontend.static.page.update');
    Route::post('/static/page/status/change/page', [PageController::class, 'updateStaticPageStatus'])->name('fontend.static.page.status.change');

    // create seo route start
    Route::get('/meta-tag', [SeoController::class, 'add'])->name('frontend.add.seo');
    Route::post('/store/seo', [SeoController::class, 'store'])->name('frontend.store.seo');
    Route::get('/edit/seo/{id}', [SeoController::class, 'edit'])->name('frontend.edit.seo');
    Route::post('/update/seo/{id}', [SeoController::class, 'update'])->name('frontend.update.seo');
    Route::post('/delete/seo', [SeoController::class, 'delete'])->name('frontend.delete.seo');



    Route::prefix('frontend')->name('frontend.')->group(function () {

        // Route for footer management
        Route::prefix('footer')->name('footer.')->group(function () {
            Route::get('/index', [AdminFooterController::class, 'index'])->name('index');
            Route::post('/content/store', [AdminFooterController::class, 'contentStore'])->name('content.store');
            Route::post('/menu/store', [AdminFooterController::class, 'menuStore'])->name('menu-store');
            Route::post('/menu/update', [AdminFooterController::class, 'update'])->name('update');
            Route::post('/menu/delete', [AdminFooterController::class, 'menuDelete'])->name('menu-delete');
            Route::post('/menu/change-status', [AdminFooterController::class, 'changeStatus'])->name('change-status');
        });
    });
});

Route::name('buyer.')->middleware('auth')->group(function () {
    Route::get('/profile', [SiteController::class, 'profile'])->name('profile');
    Route::get('/cargarage/listing/add', [SiteController::class, 'cargarage'])->name('cargarage');
    Route::get('/cargarages', [SiteController::class, 'cargarage_data'])->name('cargarage.show');
    Route::post('/buyer/listing/store', [SiteController::class, 'listing_store'])->name('listing.store');

    Route::get('/profile/information', [SiteController::class, 'info'])->name('profile.info');
    Route::get('/profile/edit', [SiteController::class, 'profile_edit'])->name('profile.edit');
    Route::get('/profile/favorite', [SiteController::class, 'profile_favorite'])->name('profile.favorite');
    Route::post('/delete/favorite', [SiteController::class, 'deleteFavorite'])->name('delete.favorite');
    Route::get('/profile/message', [SiteController::class, 'user_message'])->name('user.message');
    Route::post('buyer/message/view', [SiteController::class, 'messageCollect'])->name('message.collect');
    Route::post('buyer/message/add', [SiteController::class, 'Mess_add'])->name('byermessage.add');
    Route::post('/profile/store', [SiteController::class, 'store'])->name('profile.store');
    // route for again email verify
    Route::get('/again-verify/email', [CustomCheckAuthController::class, 'againSendVerify'])->name('again-verify.email');
});

Route::get('/generate-sitemaps', [SitemapController::class, 'generateAllSitemaps']);

Route::get('/sitemap/generate', [SitemapController::class, 'generate'])->name('sitemap');
Route::get('sitemaps/used/sitemap-index/genearate', [SitemapController::class, 'usedSitemapIndex'])->name('used.sitemap.index');

// Route::get('/sitemap.xml', [SitemapController::class, 'generate'])->name('sitemap');
// Route::get('sitemaps/used/sitemap-index.xml', [SitemapController::class, 'usedSitemapIndex'])->name('used.sitemap.index');
Route::get('sitemaps/used/sitemap-listing_{param}.xml', [SitemapController::class, 'sitemapBasedOnCity'])->name('sitemap.based.city');
Route::get('news/sitemap.xml', [SitemapController::class, 'newsGenerate'])->name('news.sitemap');
Route::get('research/sitemap.xml', [SitemapController::class, 'researchGenerate'])->name('research.sitemap');

// Route::get('/sitemap.xml', function () {
//     $sitemap = view('sitemap')->render();
//     return response($sitemap, 200)->header('Content-Type', 'application/xml');
// });

// Route::get('/sitemap.xml', function () {
//     $pages = Page::all(); // Replace with your model
//     $sitemap = view('sitemap', compact('pages'))->render();
//     return response($sitemap, 200)->header('Content-Type', 'application/xml');
// });

// ckeditor image upload route
Route::post('/ckeditor/image', [PageController::class, 'uploadImage'])->name('ckeditor.upload');

Route::get('/{slug?}', [PageController::class, 'DynamicView']);

// forntend dealer login route here
Route::get('/user/login', [DealerController::class, 'dealerLogin'])->name('frontend.dealer.login');
Route::post('/user/login/submit', [DealerController::class, 'dealerLoginSubmit'])->name('frontend.dealer.login.submit');





// 404 route
// Route::fallback(function () {
//     return view('errors_view.page_404');
// });

Route::fallback(function () {
    return response()->view('errors_view.page_404', [], 404);
});

// // 403 route
// Route::fallback(function () {
//     Log::info('Fallback route triggered for URL: ' . request()->url());
//     return response()->view('errors_view.page_403', [], 403);
// });



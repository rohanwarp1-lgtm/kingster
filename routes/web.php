<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ActivityLogController;
use App\Traits\MigrationTrait;
use App\Modules\FbaAuto\Http\Controllers\Admin\FbaAutoController;

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


Route::get('/', function () {
    $generalSettings = \App\Models\GeneralSetting::first();
    $latestProducts = \App\Models\Product::where('status', 1)->where('is_deleted', 0)
                                        ->orderBy('index', 'asc')
                                        ->take(6)
                                        ->get();
    return view('index-3', compact('generalSettings', 'latestProducts'));
})->name('index-3');

Route::get('ssd', function () { return view('ssd'); })->name('ssd');
Route::get('monitor', function () { return view('monitor'); })->name('monitor');
Route::get('airbuds', function () { return view('airbuds'); })->name('airbuds');
Route::get('keyboard', function () { return view('keyboard'); })->name('keyboard');
Route::get('laptop-stand', function () { return view('laptop-stand'); })->name('laptop.stand');
Route::get('pendrive', function () { return view('pendrive'); })->name('pendrive');

Route::get('products', function () {
    $latestProducts = \App\Models\Product::where('status', 1)->where('is_deleted', 0)->orderBy('index', 'asc')->get();
    return view('products', compact('latestProducts'));
})->name('products');


Route::get('product/{id}', function ($id) {
    $product = \App\Models\Product::findOrFail($id);
    $generalSettings = \App\Models\GeneralSetting::first();
    return view('product-details', compact('product', 'generalSettings'));
})->name('product.details');

Route::get('about-us', function () { return view('about-us'); })->name('about-us');

Route::get('shipping-returns', function () { 
    $generalSettings = \App\Models\GeneralSetting::first();
    return view('shipping-returns', compact('generalSettings')); 
})->name('shipping-returns');
Route::get('privacy-policy', function () { 
    $generalSettings = \App\Models\GeneralSetting::first();
    return view('privacy-policy', compact('generalSettings')); 
})->name('privacy-policy');
Route::get('terms-condition', function () { return view('terms-condition'); });
Route::get('contact-us', function () { 
    $generalSettings = \App\Models\GeneralSetting::first();
    return view('contact-us', compact('generalSettings')); 
})->name('contact-us');


// Warranty
Route::get('warranty-application-form', function () { return view('warranty-apply'); })->name('warranty.apply.view');
Route::get('warranty-status-lookup', function () { return view('warranty-check'); })->name('warranty.check.view');
Route::get('replacement-policy', function () { return view('warranty-rules'); })->name('warranty.replacement.policy');
Route::get('store-warranty', [WarrantyController::class, 'save'])->name('store.warranty.details');


Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');
Route::get('login', function () { return view('login'); })->name('login');
Route::get('admin', function () { return view('login'); });
Route::post('ajax-login', [CustomAuthController::class, 'loginProcess'])->name('ajax.login');


Route::middleware(['auth', \App\Http\Middleware\AutoMigrate::class])->prefix('admin')->group(function () {


        Route::get('user-management', [UserController::class, 'index'])->name('user.index');
        Route::post('user-ajax', [UserController::class, 'ajax'])->name('user.ajax');
        Route::post('user-save', [UserController::class, 'save'])->name('user.save');
        Route::get('get-user-details', [UserController::class, 'getDetails'])->name('get.user.details');
        Route::get('user-delete', [UserController::class, 'delete'])->name('delete.user');
        Route::get('user-restore', [UserController::class, 'restore'])->name('restore.user');


        Route::get('warranty-management', [CustomAuthController::class, 'warrantyManagement'])->name('admin.warranty.management');
        Route::post('warranty-ajax', [WarrantyController::class, 'ajax'])->name('warranty.ajax');
        Route::post('warranty-status-change', [WarrantyController::class, 'changeStatus'])->name('warranty.change.status');
        Route::get('warranty-delete', [WarrantyController::class, 'delete'])->name('delete.warranty');
        Route::get('warranty-restore', [WarrantyController::class, 'restore'])->name('restore.warranty');


        Route::get('product-management', [ProductController::class, 'index'])->name('product.index');
        Route::get('add-product', [ProductController::class, 'addProduct'])->name('create.product.view');
        Route::get('edit-product', [ProductController::class, 'editProduct'])->name('edit.product.view');
        Route::post('save-product', [ProductController::class, 'saveProduct'])->name('product.store');
        Route::post('product-ajax', [ProductController::class, 'productAjax'])->name('product.ajax');
        Route::get('product-delete', [ProductController::class, 'deleteProduct'])->name('product.delete');
        Route::get('product-restore', [ProductController::class, 'restoreProduct'])->name('product.restore');
        Route::post('delete-product-image', [ProductController::class, 'deleteProductImage'])->name('delete.product.image');
        Route::post('update-product-indexing', [ProductController::class, 'updateIndexing'])->name('admin.product.updateIndexing');


        Route::get('product-name-management', [ProductController::class, 'addProductName'])->name('create.product.name.view');
        Route::post('product-name-ajax', [ProductController::class, 'productNameAjax'])->name('product.name.ajax');
        Route::post('save-product-name', [ProductController::class, 'saveProductName'])->name('product.name.store');
        Route::get('product-name-details', [ProductController::class, 'productNameDetails'])->name('product.name.details');
        Route::get('product-name-delete', [ProductController::class, 'deleteProductName'])->name('delete.product.name');
        Route::get('product-name-restore', [ProductController::class, 'restoreProductName'])->name('restore.product.name');



        Route::get('activity-log', [ActivityLogController::class, 'index'])->name('admin.activity.log');

        Route::get('general-settings', [GeneralSettingController::class, 'index'])->name('general.setting');
        Route::post('general-setting-save', [GeneralSettingController::class, 'save'])->name('general.setting.save');

        Route::get('replacement-policy-editor', [GeneralSettingController::class, 'replacementPolicy'])->name('admin.replacement.policy');
        Route::post('replacement-policy-save', [GeneralSettingController::class, 'saveReplacementPolicy'])->name('admin.replacement.policy.save');

        Route::get('migrate-all-tables', function () {
            $runner = new class {
                use MigrationTrait;
            };

            return $runner->migrateAllTables();
        })->name('admin.migrate.all');

        Route::get('fba-auto', [ModuleController::class, 'fbaAutoIndex'])->name('admin.fba-auto.index');
        Route::get('fba-auto/create', [ModuleController::class, 'fbaAutoCreate'])->name('admin.fba-auto.create');
        Route::post('fba-auto/store', [ModuleController::class, 'fbaAutoStore'])->name('admin.fba-auto.store');
        Route::get('fba-auto/show/{id}', [ModuleController::class, 'fbaAutoShow'])->name('admin.fba-auto.show');
        Route::get('fba-auto/edit/{id}', [ModuleController::class, 'fbaAutoEdit'])->name('admin.fba-auto.edit');
        Route::post('fba-auto/update/{id}', [ModuleController::class, 'fbaAutoUpdate'])->name('admin.fba-auto.update');
        Route::delete('fba-auto/delete/{id}', [ModuleController::class, 'fbaAutoDelete'])->name('admin.fba-auto.destroy');
        Route::post('fba-auto/change-status/{id}', [ModuleController::class, 'fbaAutoChangeStatus'])->name('admin.fba-auto.change-status');
        Route::get('fba-auto/ajax', [ModuleController::class, 'fbaAutoAjax'])->name('admin.fba-auto.ajax');
        Route::get('fba-auto/stats', [ModuleController::class, 'fbaAutoStats'])->name('admin.fba-auto.stats');
        Route::get('fba-auto/products/search', [FbaAutoController::class, 'searchProducts'])->name('admin.fba-auto.products.search');

        // Module Routes - Warranty
        Route::get('warranty', [ModuleController::class, 'warrantyIndex'])->name('admin.warranty.index');
        Route::get('warranty/create', [ModuleController::class, 'warrantyCreate'])->name('admin.warranty.create');
        Route::post('warranty/store', [ModuleController::class, 'warrantyStore'])->name('admin.warranty.store');
        Route::get('warranty/show/{id}', [ModuleController::class, 'warrantyShow'])->name('admin.warranty.show');
        Route::post('warranty/approve/{id}', [ModuleController::class, 'warrantyApprove'])->name('admin.warranty.approve');
        Route::post('warranty/reject/{id}', [ModuleController::class, 'warrantyReject'])->name('admin.warranty.reject');
        Route::delete('warranty/delete/{id}', [ModuleController::class, 'warrantyDelete'])->name('admin.warranty.destroy');
        Route::get('warranty/ajax', [ModuleController::class, 'warrantyAjax'])->name('admin.warranty.ajax');
        Route::get('warranty/stats', [ModuleController::class, 'warrantyStats'])->name('admin.warranty.stats');

        // Module Routes - RMA
        Route::get('rma', [ModuleController::class, 'rmaIndex'])->name('admin.rma.index');
        Route::get('rma/create', [ModuleController::class, 'rmaCreate'])->name('admin.rma.create');
        Route::post('rma/store', [ModuleController::class, 'rmaStore'])->name('admin.rma.store');
        Route::get('rma/show/{id}', [ModuleController::class, 'rmaShow'])->name('admin.rma.show');
        Route::post('rma/update-status/{id}', [ModuleController::class, 'rmaUpdateStatus'])->name('admin.rma.update-status');
        Route::post('rma/assign/{id}', [ModuleController::class, 'rmaAssign'])->name('admin.rma.assign');
        Route::post('rma/comment/{id}', [ModuleController::class, 'rmaComment'])->name('admin.rma.comment');
        Route::delete('rma/delete/{id}', [ModuleController::class, 'rmaDelete'])->name('admin.rma.destroy');
        Route::get('rma/ajax', [ModuleController::class, 'rmaAjax'])->name('admin.rma.ajax');
        Route::get('rma/stats', [ModuleController::class, 'rmaStats'])->name('admin.rma.stats');

        // Module Routes - Return Report
        Route::get('return-report', [ModuleController::class, 'returnReportIndex'])->name('admin.return-report.index');
        Route::get('return-report/create', [ModuleController::class, 'returnReportCreate'])->name('admin.return-report.create');
        Route::post('return-report/store', [ModuleController::class, 'returnReportStore'])->name('admin.return-report.store');
        Route::get('return-report/show/{id}', [ModuleController::class, 'returnReportShow'])->name('admin.return-report.show');
        Route::get('return-report/dashboard', [ModuleController::class, 'returnReportDashboard'])->name('admin.return-report.dashboard');
        Route::get('return-report/export', [ModuleController::class, 'returnReportExport'])->name('admin.return-report.export');
        Route::get('return-report/filters', [ModuleController::class, 'returnReportFilters'])->name('admin.return-report.filters');
        Route::delete('return-report/delete/{id}', [ModuleController::class, 'returnReportDelete'])->name('admin.return-report.destroy');
        Route::get('return-report/ajax', [ModuleController::class, 'returnReportAjax'])->name('admin.return-report.ajax');

});

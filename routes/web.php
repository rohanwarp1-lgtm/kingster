<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\Authenticate;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarrantyController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\GeneralSettingController;
use App\Traits\MigrationTrait;

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
Route::get('admin', function () { return view('login'); })->name('login');
Route::post('ajax-login', [CustomAuthController::class, 'loginProcess'])->name('ajax.login');


Route::middleware(['auth'])->prefix('admin')->group(function () {


        Route::get('user-management', [UserController::class, 'index'])->name('user.index');
        Route::post('user-ajax', [UserController::class, 'ajax'])->name('user.ajax');
        Route::post('user-save', [UserController::class, 'save'])->name('user.save');
        Route::get('get-user-details', [UserController::class, 'userDetails'])->name('get.user.details');
        Route::get('user-status-change', [UserController::class, 'changeStatus'])->name('user.change.status');
        Route::get('user-delete', [UserController::class, 'delete'])->name('user.delete');
        Route::get('user-restore', [UserController::class, 'restore'])->name('user.restore');


        Route::get('warranty-management', [CustomAuthController::class, 'warrantyManagement'])->name('admin.warranty.management');
        Route::post('warranty-ajax', [WarrantyController::class, 'ajax'])->name('warranty.ajax');
        Route::get('warranty-status-change', [WarrantyController::class, 'changeStatus'])->name('warranty.change.status');
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



        Route::get('general-settings', [GeneralSettingController::class, 'index'])->name('general.setting');
        Route::post('general-setting-save', [GeneralSettingController::class, 'save'])->name('general.setting.save');

        Route::get('migrate-all-tables', function () {
            $runner = new class {
                use MigrationTrait;
            };

            return $runner->migrateAllTables();
        })->name('admin.migrate.all');

});

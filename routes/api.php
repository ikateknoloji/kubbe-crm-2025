<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Auth\PasswordController;
use App\Http\Controllers\V1\Auth\RoleController;
use App\Http\Controllers\V1\Product\ProductCategoryController;
use App\Http\Controllers\V1\Product\StockController;
use App\Http\Controllers\V1\Product\CategoryProductController;
use App\Http\Controllers\V1\Manufacturer\ManufacturerController;


/**
 * API Routes
 * @apiGroup Auth
 * @apiVersion 1.0.0
 * @apiName Auth
 * @apiDescription Auth API
 * @apiPermission None
 */
Route::prefix('v1/auth')->group(function () {

   Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
   Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
   Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('auth.logout');

   Route::prefix('password')->middleware('auth:sanctum')->group(function () {
       Route::post('/update', [PasswordController::class, 'update'])->name('password.update');
       Route::post('/reset', [PasswordController::class, 'reset'])->name('password.reset');
   });

   Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
       Route::post('/update-user-roles', [RoleController::class, 'updateUserRoles'])->name('roles.updateUserRoles');
   });
});


Route::prefix('v1/product')->group(function () {
    Route::apiResource('categories', ProductCategoryController::class);
});

Route::prefix('v1/product')->group(function () {
    Route::options('stocks', [StockController::class, 'index']);
    Route::post('stocks', [StockController::class, 'store']);
    Route::put('stocks/{id}', [StockController::class, 'update']);
    Route::post('stocks/{id}/decrement', [StockController::class, 'decrement']);
    Route::post('stocks/{id}/increment', [StockController::class, 'increment']);
    Route::get('stocks/zero', [StockController::class, 'zeroStock']);
    Route::get('stocks/critical', [StockController::class, 'lowStock']);
    Route::delete('stocks/{id}', [StockController::class, 'destroy']);
});

Route::prefix('v1/product')->group(function () {
    Route::get('categories', [CategoryProductController::class, 'getAllCategories']);
    Route::get('categories/{id}/product-types', [CategoryProductController::class, 'getProductTypesByCategory']);
    Route::post('stocks/find', [CategoryProductController::class, 'getStockByProductTypeAndColor']);
});

Route::apiResource('v1/manufacturers', ManufacturerController::class);

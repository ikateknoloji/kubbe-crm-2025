<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Auth\PasswordController;
use App\Http\Controllers\V1\Auth\RoleController;


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



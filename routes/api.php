<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\ExpenseController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register')->name('register');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Users API Routes
    Route::apiResource('users', UserController::class);

    // Categories API Routes
    Route::get('categories/trashed', [CategoryController::class, 'trashed'])->name('categories.trashed');
    Route::get('categories/{id}/trashed', [CategoryController::class, 'showTrashed'])->name('categories.showTrashed');
    Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('categories/{category}/force-delete', [CategoryController::class, 'forceDelete'])->name('categories.force-delete');
    Route::apiResource('categories', CategoryController::class);

    // Vendors API Routes
    Route::get('vendors/trashed', [VendorController::class, 'trashed'])->name('vendors.trashed');
    Route::get('vendors/{id}/trashed', [VendorController::class, 'showTrashed'])->name('vendors.showTrashed');
    Route::post('vendors/{id}/restore', [VendorController::class, 'restore'])->name('vendors.restore');
    Route::delete('vendors/{vendor}/force-delete', [VendorController::class, 'forceDelete'])->name('vendors.force-delete');
    Route::apiResource('vendors', VendorController::class);

    // Expenses API Routes
    Route::get('expenses/trashed', [ExpenseController::class, 'trashed'])->name('expenses.trashed');
    Route::get('expenses/{id}/trashed', [ExpenseController::class, 'showTrashed'])->name('expenses.showTrashed');
    Route::post('expenses/{id}/restore', [ExpenseController::class, 'restore'])->name('expenses.restore');
    Route::delete('expenses/{expense}/force-delete', [ExpenseController::class, 'forceDelete'])->name('expenses.force-delete');
    Route::apiResource('expenses', ExpenseController::class);

});

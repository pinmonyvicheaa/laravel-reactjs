<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require Auth)
Route::middleware(['auth:sanctum'])->group(function () {
    // Logout route - accessible to all authenticated users (admin and customer)
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin-only routes
    Route::middleware(['can:admin'])->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::post('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::patch('/products/{id}', [ProductController::class, 'restore']);

        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });
});

// // Protected Routes (Require Auth)
// Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
//     // Logout route
//     Route::post('/logout', [AuthController::class, 'logout']);

//     // Admin-only routes
//     Route::post('/products', [ProductController::class, 'store']);
//     Route::put('/products/{id}', [ProductController::class, 'update']);
//     Route::delete('/products/{id}', [ProductController::class, 'destroy']);
//     Route::patch('/products/{id}', [ProductController::class, 'restore']);

//     Route::post('/categories', [CategoryController::class, 'store']);
//     Route::put('/categories/{id}', [CategoryController::class, 'update']);
//     Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
// });
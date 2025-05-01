<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExcelController;

// Public Landing Page (accessible without login)
Route::get('/', function () {
    return view('welcome');
});

// Auth routes (provided by Breeze): /login, /register, etc.

// Protected routes – must be accessed only after login
Route::middleware(['auth'])->group(function(){
    Route::get('/dashboard', function(){
        return view('dashboard');
    })->name('dashboard');

    // Role Management CRUD (any logged‑in user)
    Route::resource('roles', RoleController::class);

    // User Account CRUD (Administrator only)
    Route::resource('users', UserController::class);

    // CRUD for Projects, Tasks, Comments (all include searching/filtering, auditing)
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('comments', CommentController::class);

    // Excel Export & Import routes
    Route::post('excel/export', [ExcelController::class, 'export']);
    Route::post('excel/import', [ExcelController::class, 'import']);
});

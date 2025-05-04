<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ExcelController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Inertia pages untuk management (Anda dapat mengembalikannya langsung atau melalui closure)
    // Route::get('/role-management', function(){
    //     return Inertia::render('RoleManagement');
    // })->name('role.management');
    Route::get('/role-management', [RoleController::class, 'index'])
    ->name('role.management');

    Route::get('/user-management', function(){
        return Inertia::render('UserManagement');
    })->name('user.management');

    Route::get('/projects', function(){
        return Inertia::render('ProjectCrud');
    })->name('project.management');

    Route::get('/tasks', function(){
        return Inertia::render('TaskCrud');
    })->name('task.management');

    Route::get('/comments', function(){
        return Inertia::render('CommentCrud');
    })->name('comment.management');

    // Resource routes untuk API/CRUD
   // Route::resource('dashboard', DashboardController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('tasks', TaskController::class);
    Route::resource('comments', CommentController::class);

    // Excel Export & Import Routes
    Route::post('excel/export', [ExcelController::class, 'export'])->name('excel.export');
    Route::post('excel/import', [ExcelController::class, 'import'])->name('excel.import');
});

require __DIR__.'/auth.php';

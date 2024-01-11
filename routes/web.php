<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdminController;
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


Route::get('/', [PageController::class, 'home']) ->  name('home.page');
Route::get('/about', [PageController::class, 'aboutUs']) ->  name('about.page');

Route::prefix('admin')->group(function () {
    Route::middleware(['route.guard'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']) -> name('admin.dashboard.page');
        Route::resource('projects', ProjectController::class);
        Route::post('/logout', [AdminController::class, 'logout']) -> name('admin.logout');
    });
    Route::get('/', [AdminController::class, 'login']) -> name('admin.login.page');
    Route::post('/auth', [AdminController::class, 'auth']) -> name('admin.auth');
});


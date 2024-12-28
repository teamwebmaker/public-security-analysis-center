<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PartnerController;
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
Route::get('/about-us', [PageController::class, 'aboutUs']) ->  name('about.page');
Route::get('/contact', [PageController::class, 'contact']) -> name('contact.page');
Route::get('/projects', [PageController::class, 'projects']) -> name('projects.page');
Route::get('/programs', [PageController::class, 'programs']) -> name('programs.page');
Route::get('/services', [PageController::class, 'services']) -> name('services.page');
Route::get('/publications', [PageController::class, 'publications']) -> name('publications.page');
Route::post('/notification', [EmailController::class, 'sendNotification']) -> name('send.notification');
Route::resource('projects', ProjectController::class) -> only('show') -> parameters([
    'projects' => 'id'
]);
Route::resource('partners', PartnerController::class) -> only('show') ;
Route::resource('publications', PublicationController::class) -> only('show') -> parameters([
    'publications' => 'id'
]);

Route::prefix('admin')->group(function () {
    Route::middleware(['route.guard'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']) -> name('admin.dashboard.page');
        Route::resource('projects', ProjectController::class) -> except('show');
        Route::resource('partners', PartnerController::class) -> except('show');
        Route::resource('publications', PublicationController::class) -> except('show');
        Route::post('/logout', [AdminController::class, 'logout']) -> name('admin.logout');
        Route::resource('contacts', ContactsController::class) -> only([
            'index', 'show', 'destroy'
        ]);
    });
    Route::get('/', [AdminController::class, 'login']) -> name('admin.login.page');
    Route::post('/auth', [AdminController::class, 'auth']) -> name('admin.auth');
});


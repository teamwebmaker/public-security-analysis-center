<?php

use App\Http\Controllers\ProgramController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;

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


Route::get('/', [PageController::class, 'home'])->name('home.page');
Route::get('/about-us', [PageController::class, 'aboutUs'])->name('about.page');
Route::get('/contact', [PageController::class, 'contact'])->name('contact.page');

Route::get('/publications', [PageController::class, 'publications'])->name('publications.page');
Route::post('/notification', [EmailController::class, 'sendNotification'])->name('send.notification');

// With Resource Controller

Route::get('/services', [PageController::class, 'services'])->name('services.page');
Route::resource('services', ServiceController::class)->only('show')->parameters([
    'services' => 'id'
]);


Route::get('/programs', [PageController::class, 'programs'])->name('programs.page');
Route::resource('programs', ProgramController::class)->only('show')->parameters([
    'programs' => 'id',
]);

Route::get('/projects', [PageController::class, 'projects'])->name('projects.page');
Route::resource('projects', ProjectController::class)->only('show')->parameters([
    'projects' => 'id'
]);
Route::resource('publications', PublicationController::class)->only('show')->parameters([
    'publications' => 'id'
]);

Route::resource('partners', controller: PartnerController::class)->only('show');

// Admin related

Route::prefix('admin')->group(function () {
    Route::middleware(['route.guard'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard.page');
        Route::resource('projects', ProjectController::class)->except('show');
        Route::resource('partners', PartnerController::class)->except('show');
        Route::resource('programs', ProgramController::class)->except('show');
        Route::resource('publications', PublicationController::class)->except('show');
        // Services 
        Route::resource('service-categories', ServiceCategoryController::class)->except('show');
        Route::resource('services', ServiceController::class)->except('show');

        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::resource('contacts', ContactsController::class)->only([
            'index',
            'show',
            'destroy'
        ]);
    });
    Route::get('/', [AdminController::class, 'login'])->name('admin.login.page');
    Route::post('/auth', [AdminController::class, 'auth'])->name('admin.auth');
});


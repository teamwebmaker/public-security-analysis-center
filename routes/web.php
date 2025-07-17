<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\MainMenuController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\UserController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Public & admin routes for the application.
|--------------------------------------------------------------------------
*/

// Public pages
Route::get('/', [PageController::class, 'home'])->name('home.page');
Route::get('/about-us', [PageController::class, 'aboutUs'])->name('about.page');
Route::get('/contact', [PageController::class, 'contact'])->name('contact.page');
Route::get('/publications', [PageController::class, 'publications'])->name('publications.page');
Route::get('/services', [PageController::class, 'services'])->name('services.page');
Route::get('/programs', [PageController::class, 'programs'])->name('programs.page');
Route::get('/projects', [PageController::class, 'projects'])->name('projects.page');

// Single resource show routes
Route::resource('services', ServiceController::class)
    ->only('show')
    ->parameters(['services' => 'id']);

Route::resource('programs', ProgramController::class)
    ->only('show')
    ->parameters(['programs' => 'id']);

Route::resource('projects', ProjectController::class)
    ->only('show')
    ->parameters(['projects' => 'id']);

Route::resource('publications', PublicationController::class)
    ->only('show')
    ->parameters(['publications' => 'id']);

Route::resource('partners', PartnerController::class)
    ->only('show');

// Contact form
Route::resource('contacts', ContactsController::class)->only('store');

// Admin routes (protected by route.guard middleware)
Route::prefix('admin')->group(function () {
    Route::middleware(['route.guard'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard.page');

        // Management related routes
        // Route::get('/register_users', [AuthController::class, 'SignUp'])->name('admin.users.index');
        Route::resource('users', UserController::class)->except('show');

        // CRUD: Projects, Partners, Publications
        Route::resource('projects', ProjectController::class)->except('show');
        Route::resource('partners', PartnerController::class)->except('show');
        Route::resource('publications', PublicationController::class)->except('show');

        // Info, Menu
        Route::resource('infos', InfoController::class)->only(['index', 'edit', 'update']);
        Route::resource('main_menus', MainMenuController::class)->except('show');

        // Programs, syllabuses, mentors (have relationship)
        Route::resource('programs', ProgramController::class)->except('show');
        Route::resource('syllabuses', SyllabusController::class)->except('show');
        Route::resource('mentors', MentorController::class)->except('show');

        // Services & Categories (have relationship)
        Route::resource('service_categories', ServiceCategoryController::class)->except('show');
        Route::resource('services', ServiceController::class)->except('show');

        // Contacts
        Route::resource('contacts', ContactsController::class)->only(['index', 'destroy']);

        // Push notifications
        Route::post('/subscribe', [PushController::class, 'saveSubscription']);
        Route::post('/unsubscribe', [PushController::class, 'unsubscribe']);
        Route::post('/check-subscription', [PushController::class, 'checkSubscription']);

        Route::get('/push-subscriptions', [PushController::class, 'index'])->name('push.index');
        Route::put('/push-subscriptions/{subscription}/approve', [PushController::class, 'approve'])->name('push.approve');
        Route::delete('/push-subscriptions/{subscription}/reject', [PushController::class, 'reject'])->name('push.reject');

        // Admin logout
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
    });

    // Admin login & auth
    Route::get('/', [AdminController::class, 'login'])->name('admin.login.page');
    Route::post('/auth', [AdminController::class, 'auth'])->name('admin.auth');
});

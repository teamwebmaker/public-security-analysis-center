<?php

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminNumberController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\DashboardRouterController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\EconomicActivityTypeController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\InstructionController;
use App\Http\Controllers\MainMenuController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsLogController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskOccurrenceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkerController;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Public & admin routes for the application.
|--------------------------------------------------------------------------
*/

// ==============
//  Public routes
// =============
Route::get('/', [PageController::class, 'home'])->name('home.page');
Route::get('/about-us', [PageController::class, 'aboutUs'])->name('about.page');
Route::get('/contact', [PageController::class, 'contact'])->name('contact.page');
Route::get('/publications', [PageController::class, 'publications'])->name('publications.page');
Route::get('/services', [PageController::class, 'services'])->name('services.page');
Route::get('/programs', [PageController::class, 'programs'])->name('programs.page');
Route::get('/projects', [PageController::class, 'projects'])->name('projects.page');

// users login & auth
Route::get('/login', [AuthController::class, 'login'])->name('login.page');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/login', [AuthController::class, 'auth'])->name('login');

// language switch
Route::get('lang/{locale}', [LanguageController::class, 'switch'])->name('lang.switch');

// Resource routes
Route::resource('publications', PublicationController::class)
    ->only('show')
    ->parameters(['publications' => 'id']);

Route::resource('projects', ProjectController::class)
    ->only('show')
    ->parameters(['projects' => 'id']);

Route::resource('programs', ProgramController::class)
    ->only('show')
    ->parameters(['programs' => 'id']);

Route::resource('services', ServiceController::class)
    ->only('show')
    ->parameters(['services' => 'id']);

Route::resource('messages', MessagesController::class)->only('store');


// ================
// Protected routes
// ===============

// Allow authorized users except admin
Route::prefix('management')
    ->as('management.')
    ->middleware(['management_users.guard'])
    ->group(function () {

        // Redirect from /management to /management/dashboard
        Route::get('/', [DashboardRouterController::class, 'redirect'])->name('dashboard.redirect');

        // Dynamically resolving the appropriate controller based on user role
        Route::get('/dashboard', [DashboardRouterController::class, 'redirectDashboard'])->name('dashboard.page');
        Route::get('/dashboard/tasks', [DashboardRouterController::class, 'redirectTask'])->name('dashboard.tasks');
    });

// Allow authorized users worker
Route::prefix('management')
    ->as('management.')
    ->middleware(['worker.guard'])
    ->group(function () {

        // Edit task status
        Route::put('tasks/{task}', [TaskController::class, 'editStatus'])->name('tasks.edit');

        // Task Document Upload
        Route::put('tasks/{task}/document', [TaskController::class, 'uploadDocument'])
            ->name('tasks.upload-document');
        Route::get('instructions', [WorkerController::class, 'displayInstructions'])->name('worker.instructions.page');
        Route::get('document-templates', [WorkerController::class, 'displayDocumentTemplates'])->name('worker.document-templates.page');

    });


Route::prefix('admin')->group(function () {

    // Admin login display & redirect
    Route::get('/', [AdminController::class, 'login'])->name('admin.login.page');
    // Admin auth
    Route::post('/auth', [AdminController::class, 'auth'])->name('admin.auth');

    // Allow authorized admin user
    Route::middleware(['admin.guard'])->group(function () {
        // Dashboard 
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard.page');

        // Sms related routes
        Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');
        Route::get('/sms/balance', [SmsController::class, 'balance']);
        Route::get('/sms/report', [SmsController::class, 'report']);

        // Management related routes register users
        Route::resource('users', UserController::class);
        Route::resource('companies', CompanyController::class)->except('show');
        Route::resource('economic_activities_types', EconomicActivityTypeController::class)
            ->parameters(['economic_activities_types' => 'economic_activity_type'])
            ->except('show');
        Route::resource('branches', BranchController::class)->except('show');
        Route::resource('tasks', TaskController::class)->except('show');
        Route::get('tasks/{task}/occurrences', [TaskController::class, 'occurrences'])
            ->name('tasks.occurrences');
        Route::resource('instructions', InstructionController::class)->except('show');
        Route::resource('document-templates', DocumentTemplateController::class)->except('show');
        Route::resource('task-occurrences', TaskOccurrenceController::class)->only(['edit', 'update', 'destroy', 'show'])->parameters(['task-occurrences' => 'taskOccurrence']);
        Route::put('task-occurrences/{taskOccurrence}/mark-paid', [TaskOccurrenceController::class, 'markPaid'])
            ->name('task-occurrences.mark-paid');
        Route::resource('admin_numbers', AdminNumberController::class)->except('show');


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

        // Messages
        Route::resource('messages', MessagesController::class)->only(['index', 'destroy']);
        Route::patch('messages/{message}/mark-read', [MessagesController::class, 'markRead'])
            ->name('messages.mark-read');
        Route::resource('sms_logs', SmsLogController::class)->except('show');

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
});

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\MainMenu;
use Illuminate\Support\Facades\View;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $MainMenu = MainMenu::orderBy('sorted', 'ASC')->with('subMenu')->get();
        // Update modified variable when js or css is updated
        View::share(['MainMenu' => $MainMenu, 'routeName' => Route::currentRouteName(), 'modified' => '03-05-2025']);
    }
}

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
        $MainMenu = MainMenu::orderBy('sorted', 'ASC') -> with('subMenu')-> get();
        View::share(['MainMenu' => $MainMenu, 'routeName' => Route::currentRouteName()]);
    }
}

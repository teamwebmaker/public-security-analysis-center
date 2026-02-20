<?php

namespace App\Providers;

use App\Models\Info;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use App\Models\MainMenu;
use Illuminate\Support\Facades\App;
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
        View::composer("*", function ($view) {
            $view->with([
                "MainMenu" => MainMenu::orderBy("sorted")->get(),
                "contactEmail" => Info::value("email"),
                "contactPhone" => Info::value("phone"),
                "language" => App::getLocale(),
            ]);
        });
    }
}

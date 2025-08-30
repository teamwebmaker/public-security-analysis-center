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
        $MainMenu = MainMenu::orderBy('sorted', 'ASC')->with('subMenu')->get();
        $language = App::getLocale();
        $contact = Info::select('email', 'phone')->first();
        View::composer('*', function ($view) {
            $view->with([
                'MainMenu' => cache()->rememberForever(
                    'main_menu',
                    fn() =>
                    MainMenu::orderBy('sorted')->with('subMenu')->get()
                ),
                'contactEmail' => Info::value('email'),
                'contactPhone' => Info::value('phone'),
                'language' => App::getLocale(),
            ]);
        });

    }
}

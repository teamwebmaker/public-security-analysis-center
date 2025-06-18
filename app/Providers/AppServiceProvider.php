<?php

namespace App\Providers;

use App\Models\Info;
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

        $contact = Info::select('email', 'phone')->first();
        View::share([
            'MainMenu' => $MainMenu,
            'contactEmail' => $contact?->email,
            'contactPhone' => $contact?->phone
        ]);
    }
}

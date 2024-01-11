<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use App\Models\Partner;
use App\Models\Info;
use App\Models\Project;
class PageController extends Controller
{
    public string $language;
    public function __construct()
    {
        $this -> language = App::getLocale();
    }

    public function home(): view
    {
        return view('pages.home', [
            'language' => $this -> language,
            'partners' => Partner::all()
        ]);
    }
    public function aboutUs(): view
    {
        return view('pages.about-us', [
            'info' => Info::first(),
        ]);
    }

    public function projects()
    {
        return view('pages.projects', [
            'projects' => Project::orderBy('id', 'DESC') -> get(),
        ]);
    }
}

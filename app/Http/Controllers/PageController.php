<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Partner;
use App\Models\Info;
use App\Models\Project;
use App\Models\Publication;
class PageController extends Controller
{
    public string $language;
    public function __construct()
    {
        $this->language = App::getLocale();
    }

    public function home()
    {
        $projects = DB::table('projects')->select('id', 'title', 'description', 'image', DB::raw('"projects" as collection'));
        $articles = DB::table('publications')->select('id', 'title', 'description', 'image', DB::raw('"publications" as collection'))
            ->union($projects)
            ->paginate(6);
        return view('pages.home', [
            'language' => $this->language,
            'articles' => $articles,
            'partners' => Partner::all()
        ]);
    }
    public function aboutUs(): view
    {
        $item = Info::all()->first();
        return view('pages.about-us', [
            'item' => $item,
            'language' => App::getLocale(),
            'partners' => Partner::all(),
            'category' => 'infos',
        ]);
    }

    public function projects(): view
    {
        return view('pages.projects', [
            'projects' => Project::orderBy('id', 'DESC')->paginate(6),
            'partners' => Partner::all()
        ]);
    }
    public function contact(): view
    {
        return view('pages.contact', [
            'partners' => Partner::all()
        ]);
    }

    public function publications()
    {
        return view('pages.publications', [
            'publications' => Publication::paginate(6),
            'partners' => Partner::all()
        ]);
    }

    public function programs()
    {
        return view('pages.programs', [
            'programs' => Program::paginate(perPage: 6),
            'partners' => Partner::all()
        ]);
    }

    public function services()
    {
        // Use paginate directly on the query, not after `all()`
        $services = Service::paginate(6);

        return view('pages.services', [
            'services' => $services,
            'partners' => Partner::all()
        ]);
    }


}

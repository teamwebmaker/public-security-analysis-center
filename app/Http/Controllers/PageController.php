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
use App\Models\ServiceCategory;

class PageController extends Controller
{
    public string $language;
    public function __construct()
    {
        $this->language = App::getLocale();
    }

  public function home(Request $request)
{
    // Determine sort order
    $sortOrder = $request->query('sort', 'newest') === 'oldest' ? 'asc' : 'desc';
    
    // Base query for publications
    $articles = DB::table('publications')
        ->select([
            'id',
            'title',
            'description',
            'image',
            'created_at',
            DB::raw('"publications" as collection')
        ]);
    
    // Union with projects query
    $combinedQuery = $articles->unionAll(
        DB::table('projects')
            ->select([
                'id',
                'title', 
                'description',
                'image',
                'created_at',
                DB::raw('"projects" as collection')
            ])
    );
    
    // Execute the paginated query
    $results = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
        ->mergeBindings($combinedQuery)
        ->orderBy('created_at', $sortOrder)
        ->paginate(6);
    
    return view('pages.home', [
        'language' => $this->language,
        'articles' => $results,
        'partners' => Partner::all(),
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
            'partners' => Partner::all(),
            'language' => App::getLocale()
        ]);
    }

    public function programs()
    {
        return view('pages.programs', [
            'programs' => Program::paginate(perPage: 6),
            'partners' => Partner::all(),
            'language' => App::getLocale()

        ]);
    }

    public function services()
    {
        $language = App::getLocale();

        // First get all categories with their services to avoid N+1 problem
        $categories = ServiceCategory::with([
            'services' => function ($query) {
                $query->orderBy('sortable');
            }
        ])->get();
        return view('pages.services', [
            'categories' => $categories,
            'partners' => Partner::all(),
            'language' => $language
        ]);
    }
}

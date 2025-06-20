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
        $sortOrder =
            $request->query("sort", "newest") === "oldest" ? "asc" : "desc";

        $articles = DB::table("publications")->select([
            "id",
            "title",
            "description",
            "image",
            "created_at",
            DB::raw('"publications" as collection'),
        ]);

        $projects = DB::table("projects")->select([
            "id",
            "title",
            "description",
            "image",
            "created_at",
            DB::raw('"projects" as collection'),
        ]);

        $programs = DB::table("programs")->select([
            "id",
            "title",
            "description",
            "image",
            "created_at",
            DB::raw('"programs" as collection'),
        ]);

        // Combine all three using unionAll
        $combinedQuery = $articles->unionAll($projects)->unionAll($programs);

        $results = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined"))
            ->mergeBindings($combinedQuery)
            ->orderBy("created_at", $sortOrder)
            ->paginate(6);

        return view("pages.home", [
            "language" => $this->language,
            "articles" => $results,
            "partners" => Partner::all(),
        ]);
    }

    public function aboutUs(): view
    {
        $item = Info::all()->first();
        return view("pages.about-us", [
            "item" => $item,
            "language" => App::getLocale(),
            "partners" => Partner::all(),
            "category" => "infos",
        ]);
    }

    public function projects(Request $request): view
    {
        $sortOrder =
            $request->query("sort", "newest") === "oldest" ? "asc" : "desc";

        return view("pages.projects", [
            "projects" => Project::where("visibility", "1")
                ->orderBy("created_at", $sortOrder)
                ->paginate(6),
            "partners" => Partner::all(),
            "language" => App::getLocale(),
        ]);
    }
    public function contact(): view
    {
        return view("pages.contact", [
            "partners" => Partner::all(),
        ]);
    }

    public function publications(Request $request)
    {
        $sortOrder =
            $request->query("sort", "newest") === "oldest" ? "asc" : "desc";
        return view("pages.publications", [
            "publications" => Publication::where("visibility", "1")
                ->orderBy("created_at", $sortOrder)
                ->paginate(6),
            "partners" => Partner::all(),
            "language" => App::getLocale(),
        ]);
    }

    public function programs(Request $request)
    {
        $sortOrder =
            $request->query("sort", "newest") === "oldest" ? "asc" : "desc";
        return view("pages.programs", [
            "programs" => Program::orderBy("created_at", $sortOrder)->paginate(
                perPage: 6
            ),
            "partners" => Partner::all(),
            "language" => App::getLocale(),
        ]);
    }

    public function services()
    {
        $language = App::getLocale();

        // First get all categories with their services to avoid N+1 problem
        $categories = ServiceCategory::with([
            "services" => function ($query) {
                $query->orderBy("sortable");
            },
        ])->get();
        return view("pages.services", [
            "categories" => $categories,
            "partners" => Partner::all(),
            "language" => $language,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Partner;
use Illuminate\Support\Facades\App;

class ProjectController extends CrudController
{
    protected string $modelClass = Project::class;
    protected string $contextField = "project";
    protected string $viewFolder = "projects";
    protected string $uploadPath = "images/projects/";

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Project::findOrFail($id);
        return view("pages.show", [
            "language" => App::getLocale(),
            "item" => $item,
            "category" => "projects",
            "partners" => Partner::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();

        $title = [
            "ka" => $data["title_ka"],
            "en" => $data["title_en"],
        ];

        $description = [
            "ka" => $data["description_ka"],
            "en" => $data["description_en"],
        ];

        $data["title"] = $title;
        $data["description"] = $description;
        $data["image"] = $this->handleImageUpload($request, "image");

        Project::create($data);

        return redirect()
            ->route("projects.index")
            ->with("success", "პროექტი შეიქმნა წარმატებით");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        $title = [
            "ka" => $data["title_ka"],
            "en" => $data["title_en"],
        ];

        $description = [
            "ka" => $data["description_ka"],
            "en" => $data["description_en"],
        ];

        $data["title"] = $title;
        $data["description"] = $description;

        $imageName = $this->handleImageUpload(
            $request,
            "image",
            $project->image
        );
        if ($imageName) {
            $data["image"] = $imageName;
        }

        $project->update($data);

        return redirect()
            ->back()
            ->with("success", "პროექტი განახლდა წარმატებით");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Partner;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class ProjectController extends CrudController
{
    protected string $modelClass = Project::class;
    protected string $contextField = "project";
    protected string $resourceName = "projects";
    protected array $fileFields = ["image" => "images/projects/"];

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
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $projectData = $this->prepareProjectData($request, $data);
        Project::create($projectData);

        return redirect()
            ->route("projects.index")
            ->with("success", "პროექტი შეიქმნა წარმატებით");
    }

    /**
     * Update an existing project.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        $projectData = $this->prepareProjectData($request, $data, $project);
        $project->update($projectData);

        return redirect()
            ->back()
            ->with("success", "პროექტი განახლდა წარმატებით");
    }

    /**
     * Extracted shared logic for preparing request data
     */
    private function prepareProjectData(Request $request, array $data, ?Project $project = null): array
    {
        // Handle image upload
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $project) {
                $existing = $project?->$field;
                $file = $this->handleFileUpload($request, $field, $path, $existing);
                return $file ? [$field => $file] : [];
            })
            ->toArray();

        // Handle translations
        $title = [
            "ka" => $data["title_ka"],
            "en" => $data["title_en"],
        ];

        $description = [
            "ka" => $data["description_ka"],
            "en" => $data["description_en"],
        ];

        return [
            ...$data,
            ...$files,
            "title" => $title,
            "description" => $description,
        ];
    }
}

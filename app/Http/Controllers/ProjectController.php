<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class ProjectController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return view('admin.projects.index', [
            'projects' => Project::orderBy('id', 'DESC') -> paginate(6),
            'routeName' => Route::currentRouteName()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.projects.create', [
            'routeName' => Route::currentRouteName()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $data = $request->validated();
        $image = $request -> image;
        $imageName = uniqid() . '-' . time() .'.'. $image -> extension(); // TODO: Generate new File Name
        $uploadPath = 'images/projects/'; //TODO: Set Upload Path
        $image->move(public_path($uploadPath), $imageName); //TODO: Store File in Public Directory
        $title = ["ka" => $data['title_ka'], "en" => $data['title_en']];
        $description = ["ka" => $data['description_ka'], "en" => $data['description_en']];
        Project::create([
            'title' => $title,
            'description' => $description,
            'image' => $imageName
        ]);
        return redirect() -> route('projects.index') -> with('success', 'პროექტი შეიქმნა წარმატებით');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Project::findOrFail($id);
        return view('pages.show', [
            'language' => App::getLocale(),
            'item' => $item,
            'category' => 'projects',
            'partners' => Partner::all()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        return view('admin.projects.edit',[
            'project' => $project,
            'routeName' => Route::currentRouteName()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {   $imageName = null;
        $data = $request->validated();
        if($request->hasFile('image')){
            $image = $request -> image;
            $imageName = uniqid() . '-' . time() .'.'. $image -> extension(); // TODO: Generate new File Name
            $uploadPath = 'images/projects/'; //TODO: Set Upload Path
            $image->move(public_path($uploadPath), $imageName); //TODO: Store File in Public Directory
        }
        $title = ["ka" => $data['title_ka'], "en" => $data['title_en']];
        $description = ["ka" => $data['description_ka'], "en" => $data['description_en']];
        $updatedData = [
            'title' => $title,
            'description' => $description,
        ];
        if ($imageName) $updatedData = [...$updatedData, 'image' => $imageName];
        $project->update($updatedData);
        return redirect() -> back() -> with('success', 'პროექტი განახლდა წარმატებით');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project -> delete();
        return redirect() -> route('projects.index') -> with('success', 'პროექტი წარმატებით წაიშალა.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramsRequest;
use App\Http\Requests\UpdateProgramsRequest;
use App\Models\Partner;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

class ProgramController extends CrudController
{
    protected string $modelClass = Program::class;
    protected string $contextField = "program";
    protected string $viewFolder = "programs";
    protected string $uploadPath = "images/programs/";

    protected array $imageFields = [
        "image" => "images/programs/",
        "certificate_image" => "images/certificates/programs/",
    ];
    /**
     * Display a listing of the resource.
     */

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreProgramsRequest $request)
    {
        $data = $request->validated();

        foreach ($this->imageFields as $field => $path) {
            $imageName = $this->handleImageUpload($request, $field, $path);
            if ($imageName) {
                $data[$field] = $imageName;
            }
        }

        // Create a translation map for week days
        // Take data from input value replace value with english word 
        // And instead of georgian name as key set english as key and georgian as value
        $weekDayTranslations = [
            "ორშაბათი" => "Monday",
            "სამშაბათი" => "Tuesday",
            "ოთხშაბათი" => "Wednesday",
            "ხუთშაბათი" => "Thursday",
            "პარასკევი" => "Friday",
            "შაბათი" => "Saturday",
            "კვირა" => "Sunday",
        ];

        // Get the submitted days (or empty array if not set)
        $submittedDays = $data["days"] ?? [];

        // Populate the en key values with the translated values
        $daysData = [
            "ka" => $submittedDays,
            "en" => array_map(function ($day) use ($weekDayTranslations) {
                return $weekDayTranslations[$day] ?? $day; // Fallback to original if translation missing
            }, $submittedDays),
        ];

        $programData = [
            "title" => $data["title"], // This will be an array ['ka' => ..., 'en' => ...]
            "description" => $data["description"], // Same array structure
            "image" => $data["image"] ?? null,
            "certificate_image" => $data["certificate_image"] ?? null,
            "video" => $data["video"],
            "price" => $data["price"],
            "duration" => $data["duration"],
            "address" => $data["address"],
            "start_date" => $data["start_date"],
            "end_date" => $data["end_date"],
            "hour" => $data["hour"],
            "days" => $daysData,
            "visibility" => $data["visibility"],
        ];

        Program::create($programData);

        return redirect()
            ->route("programs.index")
            ->with("success", "პროგრამა შეიქმნა წარმატებით");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Program::with(["syllabuses", "mentors"])->findOrFail($id);
        // dd(vars: );
        return view("pages.show-program", [
            "item" => $item,
            "language" => App::getLocale(),
            "partners" => Partner::all(),
            "category" => "programs",
            // 'mentors' => $mentors
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProgramsRequest $request, Program $program)
    {
           $data = $request->validated();

        foreach ($this->imageFields as $field => $path) {
            $imageName = $this->handleImageUpload($request, $field, $path, $program->image);
            if ($imageName) {
                $data[$field] = $imageName;
            }
        }

        // Create a translation map for week days
        // Take data from input value replace value with english word 
        // And instead of georgian name as key set english as key and georgian as value
        $weekDayTranslations = [
            "ორშაბათი" => "Monday",
            "სამშაბათი" => "Tuesday",
            "ოთხშაბათი" => "Wednesday",
            "ხუთშაბათი" => "Thursday",
            "პარასკევი" => "Friday",
            "შაბათი" => "Saturday",
            "კვირა" => "Sunday",
        ];

        // Get the submitted days (or empty array if not set)
        $submittedDays = $data["days"] ?? [];

        // Populate the en key values with the translated values
        $daysData = [
            "ka" => $submittedDays,
            "en" => array_map(function ($day) use ($weekDayTranslations) {
                return $weekDayTranslations[$day] ?? $day; // Fallback to original if translation missing
            }, $submittedDays),
        ];

        $programData = [
            "title" => $data["title"], // This will be an array ['ka' => ..., 'en' => ...]
            "description" => $data["description"], // Same array structure
            "video" => $data["video"],
            "price" => $data["price"],
            "duration" => $data["duration"],
            "address" => $data["address"],
            "start_date" => $data["start_date"],
            "end_date" => $data["end_date"],
            "hour" => $data["hour"],
            "days" => $daysData,
            "visibility" => $data["visibility"],
        ];

        $program->update($programData);

        return redirect()
            ->back()
            ->with("success", "პროგრამა განახლდა წარმატებით");
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProgramsRequest;
use App\Http\Requests\UpdateProgramsRequest;
use App\Models\Partner;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ProgramController extends CrudController
{
    // Core metadata for CRUD operations
    protected string $modelClass = Program::class;
    protected string $contextField = "program";

    protected string $contextFieldPlural = "programs";
    protected string $resourceName = "programs";
    protected string $uploadPath = "images/programs/";

    // Image fields and their upload directories
    protected array $fileFields = [
        "image" => "images/programs/",
        "certificate_image" => "images/certificates/programs/",
    ];

    // Static map for translating weekday names from Georgian to English
    public const WEEKDAY_TRANSLATIONS = [
        "ორშაბათი" => "Monday",
        "სამშაბათი" => "Tuesday",
        "ოთხშაბათი" => "Wednesday",
        "ხუთშაბათი" => "Thursday",
        "პარასკევი" => "Friday",
        "შაბათი" => "Saturday",
        "კვირა" => "Sunday",
    ];

    // Handles storing a new program
    public function store(StoreProgramsRequest $request)
    {
        $data = $request->validated();
        $programData = $this->prepareProgramData($request, $data);
        Program::create($programData);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "პროგრამა შეიქმნა წარმატებით");
    }

    // Handles updating an existing program
    public function update(UpdateProgramsRequest $request, Program $program)
    {
        $data = $request->validated();
        $programData = $this->prepareProgramData($request, $data, $program);
        $program->update($programData);

        return redirect()
            ->back()
            ->with("success", "პროგრამა განახლდა წარმატებით");
    }

    // Displays the details of a single program
    public function show(string $id)
    {
        $item = Program::with(["syllabuses", "mentors"])->findOrFail($id);

        return view("pages.show-program", [
            "item" => $item,
            "language" => App::getLocale(),
            "partners" => Partner::all(),
            "category" => "programs",
            'program_syllabuses' => 'syllabuses'
        ]);
    }

    /**
     * Extracts and prepares program data from the request
     * Handles:
     * - Uploading new images (with optional deletion of old images)
     * - Translating submitted weekdays to English
     * - Merging all into a single array using PHP's spread operator
     *
     * @param Request $request The incoming HTTP request
     * @param array $data The validated request data
     * @param Program|null $program The program model for updates (optional)
     * @return array Fully prepared array of attributes for DB insert/update
     */
    private function prepareProgramData(Request $request, array $data, ?Program $program = null): array
    {
        // Process and upload each image field; delete old if new one is uploaded
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $program) {
                $existing = $program?->$field;
                $file = $this->handleFileUpload($request, $field, $path, $existing);
                return $file ? [$field => $file] : [];
            })
            ->toArray();

        // Process the submitted weekdays
        $submittedDays = $data["days"] ?? [];

        // Build a multilingual "days" field (Georgian and translated English)
        $daysData = [
            "ka" => $submittedDays,
            "en" => array_map(fn($day) => self::WEEKDAY_TRANSLATIONS[$day] ?? $day, $submittedDays),
        ];

        $title = [
            "ka" => $data["title_ka"],
            "en" => $data["title_en"],
        ];

        $description = [
            "ka" => $data["description_ka"],
            "en" => $data["description_en"],
        ];

        $hour = [
            "start" => $data["hour_start"],
            "end" => $data["hour_end"],
        ];

        // Return merged data: uploaded images + other fields + translated days
        return [
            ...$files,
            "title" => $title,
            "description" => $description,
            "video" => $data["video"],
            "price" => $data["price"],
            "duration" => $data["duration"],
            "address" => $data["address"],
            "start_date" => $data["start_date"],
            "end_date" => $data["end_date"],
            "hour" => $hour,
            "days" => $daysData,
            "visibility" => $data["visibility"],
        ];
    }
}

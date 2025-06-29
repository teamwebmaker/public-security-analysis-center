<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMentorRequest;
use App\Http\Requests\UpdateMentorRequest;
use App\Models\Mentor;
use Illuminate\Http\Request;

class MentorController extends CrudController
{
    protected string $modelClass = Mentor::class;
    protected string $contextField = "mentor";
    protected string $contextFieldPlural = "mentors";
    protected string $resourceName = "mentors";

    protected array $fileFields = [
        "image" => "images/mentors/",
    ];

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMentorRequest $request)
    {
        $data = $request->validated();
        $mentorData = $this->prepareMentorData($request, $data);
        Mentor::create($mentorData);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "მენტორი შეიქმნა წარმატებით");
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMentorRequest $request, Mentor $mentor)
    {
        $data = $request->validated();
        $mentorData = $this->prepareMentorData($request, $data, $mentor);
        $mentor->update($mentorData);

        return redirect()
            ->back()
            ->with("success", "მენტორი განახლდა წარმატებით");
    }

    /**
     * Extract shared logic for preparing $mentor data
     */
    private function prepareMentorData(Request $request, array $data, ?Mentor $mentor = null): array
    {
        // Handle file uploads
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $mentor) {
                $existing = $mentor?->$field;
                $file = $this->handleFileUpload($request, $field, $path, $existing);
                return $file ? [$field => $file] : [];
            })
            ->toArray();

        $description = [
            "ka" => $data["description_ka"],
            "en" => $data["description_en"],
        ];

        return [
            ...$data,
            ...$files,
            "description" => $description,
        ];
    }
}

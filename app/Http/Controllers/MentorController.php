<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMentorRequest;
use App\Http\Requests\UpdateMentorRequest;
use App\Models\Mentor;
use App\Models\Program;
use Illuminate\Http\Request;

class MentorController extends CrudController
{
    protected string $modelClass = Mentor::class;
    protected string $contextField = "mentor";
    protected string $contextFieldPlural = "mentors";
    protected array $belongsTo = ["programs"];
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
        // dd($data);
        $mentorData = $this->prepareMentorData($request, $data);
        $mentor = $this->modelClass::create($mentorData);


        if (!empty($data['program_ids'])) {
            $mentor->programs()->sync($data['program_ids']);
        }


        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "მენტორი შეიქმნა წარმატებით");
    }

    /**
     * Pass additional data to create view.
     */
    protected function additionalCreateData(): array
    {
        return $this->getProgramFormData();
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateMentorRequest $request,
        Mentor $mentor
    ) {

        $data = $request->validated();
        $mentorData = $this->prepareMentorData($request, $data, $mentor);
        $mentor->update($mentorData);

        // Sync programs (remove ones unchecked, add new ones)
        $mentor->programs()->sync($request->input('program_ids', []));

        return redirect()
            ->back()
            ->with("success", "მენტორი განახლდა წარმატებით");
    }


    /**
     * Pass additional data to edit view.
     */
    protected function additionalEditData(): array
    {
        return $this->getProgramFormData();
    }


    /**
     * Shared data for create/edit service forms.
     */
    private function getProgramFormData(): array
    {
        return [
            'programs' => Program::select('id', 'title')->get(),
        ];
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

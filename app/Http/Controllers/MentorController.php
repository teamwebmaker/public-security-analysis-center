<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreMentorRequest;
use App\Http\Requests\UpdateMentorRequest;
use App\Models\Mentor;
use App\Models\Program;
use Illuminate\Http\Request;

class MentorController extends CrudController
{
    use SyncsRelations;
    protected string $modelClass = Mentor::class;
    protected string $contextField = "mentor";
    protected string $contextFieldPlural = "mentors";
    protected array $modelRelations = ["programs"];
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
        $mentor = $this->modelClass::create($mentorData);

        // Sync programs (remove unchecked)
        $this->syncRelations($mentor, $data, [
            'programs' => 'program_ids',
        ]);

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

        // Sync programs (remove unchecked)
        $this->syncRelations($mentor, $data, [
            'programs' => 'program_ids',
        ]);

        $mentor->update($mentorData);

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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSyllabusRequest;
use App\Http\Requests\UpdateSyllabusRequest;
use App\Models\Program;
use App\Models\Syllabus;
use Illuminate\Http\Request;

class SyllabusController extends CrudController
{
    protected string $modelClass = Syllabus::class;
    protected string $contextField = "syllabus";
    protected string $contextFieldPlural = "syllabuses";
    protected string $resourceName = "syllabuses";

    protected array $belongsTo = ["program"];
    protected array $fileFields = ["pdf" => "documents/syllabuses/",];


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSyllabusRequest $request)
    {
        $data = $request->validated();
        $syllabusData = $this->prepareSyllabusData($request, $data);
        Syllabus::create($syllabusData);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "სილაბუსი შეიქმნა წარმატებით");
    }

    /**
     * Pass additional data to create view.
     */
    protected function additionalCreateData(): array
    {
        return $this->getSyllabusFormData();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSyllabusRequest $request, Syllabus $syllabus)
    {
        $data = $request->validated();
        $syllabusData = $this->prepareSyllabusData($request, $data, $syllabus);
        $syllabus->update($syllabusData);

        return redirect()
            ->back()
            ->with("success", "სილაბუსი განახლდა წარმატებით");
    }

    /**
     * Add additional data to update view.
     */
    protected function additionalEditData(): array
    {
        return $this->getSyllabusFormData();
    }


    /**
     * Shared data for create/edit service forms.
     */
    private function getSyllabusFormData(): array
    {
        return [
            'programs' => Program::all()->pluck('title.ka', 'id')->toArray(),
        ];
    }


    /**
     * Extracted shared logic for preparing request data
     */
    private function prepareSyllabusData(
        Request $request,
        array $data,
        ?Syllabus $syllabus = null
    ): array {
        // Handle image upload
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $syllabus) {
                $existing = $syllabus?->$field;
                $file = $this->handleFileUpload(
                    $request,
                    $field,
                    $path,
                    $existing
                );
                return $file ? [$field => $file] : [];
            })
            ->toArray();

        // Handle translations
        $title = [
            "ka" => $data["title_ka"],
            "en" => $data["title_en"],
        ];
        return [
            ...$data,
            ...$files,
            "title" => $title,
        ];
    }
}

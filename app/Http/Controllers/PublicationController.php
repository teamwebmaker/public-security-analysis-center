<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Models\Partner;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class PublicationController extends CrudController
{
    protected string $modelClass = Publication::class;
    protected string $contextField = "publication";
    protected string $contextFieldPlural = "publications";
    protected string $resourceName = "publications";
    protected array $fileFields = ["image" => "images/publications/", "file" => "documents/publications/"];
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = $this->modelClass::findOrFail($id);
        return view("pages.show", [
            "item" => $item,
            "category" => $this->resourceName,
            "partners" => Partner::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePublicationRequest $request)
    {
        $data = $request->validated();
        $publicationData = $this->preparePublicationData($request, $data);
        $this->modelClass::create($publicationData);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "პუბლიკაცია შეიქმნა წარმატებით");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePublicationRequest $request, Publication $publication)
    {
        $data = $request->validated();
        $publicationData = $this->preparePublicationData($request, $data, $publication);
        $publication->update($publicationData);

        return redirect()
            ->back()
            ->with("success", "პუბლიკაცია განახლდა წარმატებით");
    }

    /**
     * Extracted shared logic for preparing request data
     */
    private function preparePublicationData(
        Request $request,
        array $data,
        ?Publication $publication = null
    ): array {
        // Handle image upload
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $publication) {
                $existing = $publication?->$field;
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

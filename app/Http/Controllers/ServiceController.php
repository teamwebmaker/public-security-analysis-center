<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Partner;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ServiceController extends CrudController
{
    protected string $modelClass = Service::class;
    protected string $contextField = "service";
    protected string $contextFieldPlural = "services";

    protected array $belongsTo = ["category"];
    protected string $resourceName = "services";
    protected array $fileFields = ["image" => "images/services/"];

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = $this->modelClass::findOrFail($id);
        return view("pages.show", [
            "language" => App::getLocale(),
            "item" => $item,
            "category" => "services",
            "partners" => Partner::all(),
        ]);
    }

    /**
     * Pass additional data to index view.
     */
    protected function additionalIndexData(): array
    {
        $reqBranchId = request()->query()
            ? (int) array_keys(request()->query())[0]
            : null;
        return [
            "serviceCategories" => ServiceCategory::all(),
            "selectedServiceId" => $reqBranchId,
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $serviceData = $this->prepareServiceData($request, $data);
        $this->modelClass::create($serviceData);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "სერვისი შეიქმნა წარმატებით");
    }

    /**
     * Pass additional data to create view.
     */
    protected function additionalCreateData(): array
    {
        return $this->getServiceFormData();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        $projectData = $this->prepareServiceData($request, $data, $service);
        $service->update($projectData);

        return redirect()
            ->back()
            ->with("success", "სერვისი განახლდა წარმატებით");
    }

    /**
     * Add additional data to update view.
     */
    protected function additionalEditData(): array
    {
        return $this->getServiceFormData();
    }

    /**
     * Shared data for create/edit service forms.
     */
    private function getServiceFormData(): array
    {
        return [
            "serviceCategories" => ServiceCategory::all()
                ->pluck("name.ka", "id")
                ->toArray(),
            "services" => Service::select(
                "id",
                "service_category_id",
                "sortable"
            )->get(),
        ];
    }

    /**
     * Prepare service data for storing or updating.
     */
    private function prepareServiceData(
        Request $request,
        array $data,
        ?Service $service = null
    ): array {
        // Handle image upload
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $service) {
                $existing = $service?->$field;
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

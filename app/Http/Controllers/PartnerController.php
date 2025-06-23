<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnersRequest;
use App\Http\Requests\UpdatePartnersRequest;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends CrudController
{
    protected string $modelClass = Partner::class;
    protected string $contextField = "partner";
    protected string $contextFieldPlural = "partners";
    protected string $resourceName = "partners";

    protected array $fileFields = [
        "image" => "images/partners/",
    ];

    /**
     * Store a newly created partner.
     */
    public function store(StorePartnersRequest $request)
    {
        $data = $request->validated();
        $partnerData = $this->preparePartnerData($request, $data);

        Partner::create($partnerData);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "პარტნიორი დაემატა წარმატებით");
    }

    /**
     * Update an existing partner.
     */
    public function update(UpdatePartnersRequest $request, Partner $partner)
    {
        $data = $request->validated();
        $partnerData = $this->preparePartnerData($request, $data, $partner);

        $partner->update($partnerData);

        return redirect()
            ->back()
            ->with("success", "პარტნიორი განახლდა წარმატებით");
    }

    /**
     * Extract shared logic for preparing partner data
     */
    private function preparePartnerData(Request $request, array $data, ?Partner $partner = null): array
    {
        // Handle file uploads
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $partner) {
                $existing = $partner?->$field;
                $file = $this->handleFileUpload($request, $field, $path, $existing);
                return $file ? [$field => $file] : [];
            })
            ->toArray();

        return [
            ...$data,
            ...$files,
        ];
    }
}

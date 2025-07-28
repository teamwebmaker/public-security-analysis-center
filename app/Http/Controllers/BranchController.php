<?php

namespace App\Http\Controllers;

use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Models\Company;

class BranchController extends CrudController
{
    protected string $modelClass = Branch::class;
    protected string $contextField = "branch";
    protected string $contextFieldPlural = "branches";
    protected string $resourceName = "branches";
    protected array $belongsTo = ["company"];

    protected function additionalIndexData(): array
    {
        $reqBranchId = request()->query()
            ? (int) array_keys(request()->query())[0]
            : null;
        return [
            "selectedBranchId" => $reqBranchId,
        ];
    }
    protected function additionalCreateData(): array
    {
        return $this->getCompanyFormData();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BranchRequest $request)
    {
        $data = $request->validated();
        $this->modelClass::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "ფილიალი შეიქმნა წარმატებით");
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(BranchRequest $request, Branch $branch)
    {
        $data = $request->validated();
        $branch->update($data);

        return redirect()
            ->back()
            ->with("success", "ფილიალი განახლდა წარმატებით");
    }

    protected function additionalEditData(): array
    {
        return $this->getCompanyFormData();
    }

    public function getCompanyFormData()
    {
        return [
            "companies" => Company::all()
                ->pluck("name", "id")
                ->toArray(),
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\EconomicActivityType;

class CompanyController extends CrudController
{
    protected string $modelClass = Company::class;
    protected string $contextField = "company";
    protected string $contextFieldPlural = "companies";
    protected string $resourceName = "companies";
    protected array $modelRelations = ['economic_activity_type'];

    protected function additionalCreateData(): array
    {
        return $this->getEconomicActivityTypeFormData();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreCompanyRequest $request
    ) {
        $data = $request->validated();
        $this->modelClass::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "კომპანია შეიქმნა წარმატებით");
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $data = $request->validated();
        $company->update($data);

        return redirect()
            ->back()
            ->with("success", "კომპანია განახლდა წარმატებით");
    }

    protected function additionalEditData(): array
    {
        return $this->getEconomicActivityTypeFormData();
    }

    public function getEconomicActivityTypeFormData()
    {
        return [
            'economic_activity_types' => EconomicActivityType::all()->pluck('display_name', 'id')->toArray()
        ];
    }
}

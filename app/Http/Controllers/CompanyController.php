<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\EconomicActivityType;
use App\Models\Role;
use App\Models\User;

class CompanyController extends CrudController
{
    use SyncsRelations;
    protected string $modelClass = Company::class;
    protected string $contextField = "company";
    protected string $contextFieldPlural = "companies";
    protected string $resourceName = "companies";
    protected array $modelRelations = ['economic_activity_type', 'users', 'branches'];

    protected function additionalCreateData(): array
    {
        return $this->getFormData();
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreCompanyRequest $request
    ) {
        $data = $request->validated();
        $company = $this->modelClass::create($data);
        // dd($data, $company);

        $this->syncRelations($company, $data, [
            'users' => 'user_ids',
        ]);

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
        $this->syncRelations($company, $data, [
            'users' => 'user_ids',
        ]);

        return redirect()
            ->back()
            ->with("success", "კომპანია განახლდა წარმატებით");
    }

    protected function additionalEditData(): array
    {
        return $this->getFormData();
    }

    public function getFormData()
    {
        $roleId = Role::where('name', 'company_leader')->value('id');
        return [
            'economic_activity_types' => EconomicActivityType::all()->pluck('display_name', 'id')->toArray(),
            "users" => User::where('role_id', $roleId)->select('id', 'full_name')->get()
        ];
    }
}

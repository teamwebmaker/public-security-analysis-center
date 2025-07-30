<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;

class BranchController extends CrudController
{
    use SyncsRelations;
    protected string $modelClass = Branch::class;
    protected string $contextField = "branch";
    protected string $contextFieldPlural = "branches";
    protected string $resourceName = "branches";
    protected array $modelRelations = ["users", "company"];

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
        $branch = $this->modelClass::create($data);

        $this->syncRelations($branch, $data, [
            'users' => 'user_ids',
        ]);

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

        $this->syncRelations($branch, $data, [
            'users' => 'user_ids',
        ]);

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
        $roleId = Role::where('name', 'responsible_person')->value('id');
        return [
            "companies" => Company::pluck("name", "id")->toArray(),
            "users" => User::where('role_id', $roleId)->select('id', 'full_name')->get()
        ];
    }

}

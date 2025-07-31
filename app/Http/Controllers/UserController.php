<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends CrudController
{

    use SyncsRelations;
    protected string $modelClass = User::class;
    protected string $contextField = "user";
    protected string $contextFieldPlural = "users";
    protected string $resourceName = "users";
    protected array $modelRelations = ['role', 'companies', 'branches'];

    protected array $localScopes = ['withoutAdmins'];


    protected function additionalCreateData(): array
    {
        return $this->prepareRoleData();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $user = $this->modelClass::create($data);

        // Sync companies and branches (remove ones unchecked)
        $this->syncRelations($user, $data, [
            'companies' => 'company_ids',
            'branches' => 'branch_ids',
            'services' => 'service_ids'
        ]);


        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "მომხმარებელი შეიქმნა წარმატებით");
    }


    protected function additionalEditData(): array
    {
        return $this->prepareRoleData();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);


        // Sync companies and branches (remove ones unchecked)
        $this->syncRelations($user, $data, [
            'companies' => 'company_ids',
            'branches' => 'branch_ids',
            'services' => 'service_ids'
        ]);

        return redirect()
            ->back()
            ->with("success", "მომხმარებელი განახლდა წარმატებით");
    }

    protected function prepareRoleData()
    {
        return [
            'roles' => Role::WithoutAdmins()->get(),
            'companies' => Company::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'services' => Service::select('id', 'title')->get()
        ];
    }
}

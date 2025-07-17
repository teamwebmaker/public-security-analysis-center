<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends CrudController
{
    protected string $modelClass = User::class;
    protected string $contextField = "user";
    protected string $contextFieldPlural = "users";
    protected string $resourceName = "users";
    protected array $belongsTo = ['role'];

    protected array $localScopes = ['withoutAdmins'];


    protected function additionalCreateData(): array
    {
        return [
            'roles' => $this->prepareRoleData(),
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        User::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "მომხმარებელი შეიქმნა წარმატებით");
    }


    protected function additionalEditData(): array
    {
        return [
            'roles' => $this->prepareRoleData(),
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);

        return redirect()
            ->back()
            ->with("success", "მომხმარებელი განახლდა წარმატებით");
    }

    protected function prepareRoleData()
    {
        return Role::WithoutAdmins()->get()->pluck('display_name', 'id')->toArray();
    }

}

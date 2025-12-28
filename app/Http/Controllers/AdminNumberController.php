<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminNumberRequest;
use App\Http\Requests\UpdateAdminNumberRequest;
use App\Models\AdminNumber;

class AdminNumberController extends CrudController
{
    protected string $modelClass = AdminNumber::class;
    protected string $contextField = "admin_number";
    protected string $contextFieldPlural = "admin_numbers";
    protected string $resourceName = "admin_numbers";

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminNumberRequest $request)
    {
        $data = $request->validated();
        $this->modelClass::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "ნომერი დაემატა წარმატებით");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminNumberRequest $request, AdminNumber $admin_number)
    {
        $data = $request->validated();
        $admin_number->update($data);

        return redirect()
            ->back()
            ->with("success", "ნომერი განახლდა წარმატებით");
    }
}

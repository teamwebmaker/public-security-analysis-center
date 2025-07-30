<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEconomicActivityTypeRequest;
use App\Http\Requests\UpdateEconomicActivityTypeRequest;
use App\Models\EconomicActivityType;
use Illuminate\Http\Request;

class EconomicActivityTypeController extends CrudController
{
    protected string $modelClass = EconomicActivityType::class;
    protected string $contextField = "economic_activity_type";
    protected string $contextFieldPlural = "economic_activities_types";
    protected string $resourceName = "economic_activities_types";
    protected array $modelRelations = ['companies'];

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEconomicActivityTypeRequest $request)
    {
        $data = $request->validated();
        $this->modelClass::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "ეკონომიკური საქმიანობის ტიპი წარმატებით შეიქმნა");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEconomicActivityTypeRequest $request, EconomicActivityType $economic_activity_type)
    {
        $data = $request->validated();
        $economic_activity_type->update($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "ეკონომიკური საქმიანობის ტიპი წარმატებით განახლდა");
    }
}

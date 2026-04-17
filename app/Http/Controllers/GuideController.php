<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGuideRequest;
use App\Http\Requests\UpdateGuideRequest;
use App\Models\Guide;

class GuideController extends CrudController
{
    protected string $modelClass = Guide::class;
    protected string $contextField = 'guide';
    protected string $contextFieldPlural = 'guides';
    protected string $resourceName = 'guides';
    protected string $defaultOrderBy = 'sort_order';
    protected string $defaultOrderDirection = 'asc';

    public function store(StoreGuideRequest $request)
    {
        $data = $request->validated();
        $this->modelClass::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with('success', 'გზამკვლევი შეიქმნა წარმატებით');
    }

    public function update(UpdateGuideRequest $request, Guide $guide)
    {
        $data = $request->validated();
        $guide->update($data);

        return redirect()
            ->back()
            ->with('success', 'გზამკვლევი განახლდა წარმატებით');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceCategoriesRequest;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceCategoryController extends CrudController
{
    protected string $modelClass = ServiceCategory::class;
    protected string $contextField = "serviceCategory";
    protected string $contextFieldPlural = "serviceCategories";
    protected string $resourceName = "service_categories";


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceCategoriesRequest $request)
    {
        $data = $request->validated();
        $serviceCategoryData = $this->prepareServiceCategoryData($data);
        ServiceCategory::create($serviceCategoryData);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "კატეგორია დაემატა წარმატებით");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreServiceCategoriesRequest $request, ServiceCategory $serviceCategory)
    {
        $data = $request->validated();
        $serviceCategoryData = $this->prepareServiceCategoryData($data);
        $serviceCategory->update($serviceCategoryData);

        return redirect()
            ->back()
            ->with("success", "კატეგორია განახლდა წარმატებით");
    }

    public function prepareServiceCategoryData(array $data, )
    {
        // Handle translations
        $name = [
            "ka" => $data["name_ka"],
            "en" => $data["name_en"],
        ];

        return [
            ...$data,
            "name" => $name,
        ];
    }

}

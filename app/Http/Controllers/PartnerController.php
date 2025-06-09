<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnersRequest;
use App\Http\Requests\UpdatePartnersRequest;
use App\Models\Partner;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PartnerController extends CrudController
{
    protected string $modelClass = Partner::class;
    protected string $contextField = "partner";
    protected string $viewFolder = "partners";
    protected string $uploadPath = "images/partners/";

    public function store(StorePartnersRequest $request)
    {
        $data = $request->validated();

        $data["image"] = $this->handleImageUpload($request, "image");

        Partner::create($data);

        return redirect()
            ->route("partners.index")
            ->with("success", "პარტნიორი დაემატა წარმატებით");
    }

    public function update(UpdatePartnersRequest $request, Partner $partner)
    {
        $data = $request->validated();

        $imageName = $this->handleImageUpload(
            $request,
            "image",
            $partner->image
        );
        if ($imageName) {
            $data["image"] = $imageName;
        }
        $partner->update($data);

        return redirect()
            ->back()
            ->with("success", "პარტნიორი განახლდა წარმატებით");
    }
}

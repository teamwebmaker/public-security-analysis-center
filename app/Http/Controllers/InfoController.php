<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateInfoRequest;
use App\Models\Info;


class InfoController extends CrudController
{
    protected string $modelClass = Info::class;
    protected string $contextField = "info";
    protected string $contextFieldPlural = "infos";
    protected string $resourceName = "infos";
    protected array $fileFields = ["image" => "images/infos/"];
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInfoRequest $request, Info $info)
    {
        $data = $request->validated();
        // Handle image upload
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $info) {
                $existing = $info?->$field;
                $file = $this->handleFileUpload($request, $field, $path, $existing);
                return $file ? [$field => $file] : [];
            })
            ->toArray();

        // Handle translations
        $title = [
            "ka" => $data["title_ka"],
            "en" => $data["title_en"],
        ];

        $description = [
            "ka" => $data["description_ka"],
            "en" => $data["description_en"],
        ];

        $infoData = [
            ...$data,
            ...$files,
            "title" => $title,
            "description" => $description,
        ];
        $info->update($infoData);

        return redirect()
            ->back()
            ->with("success", "ინფორმაცია განახლდა წარმატებით");
    }
}

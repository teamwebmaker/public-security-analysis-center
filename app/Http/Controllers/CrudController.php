<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

abstract class CrudController extends Controller
{
    protected string $modelClass;
    protected string $contextField;
    protected string $contextFieldPlural;

    protected array $belongsTo = [];

    protected string $resourceName;

    // Remember field names should be the same as in database and in input form
    protected array $fileFields = []; // In case if user needs to upload multiple files in different paths (optional)

    // Apply default sorting and make it changeable if needed
    protected string $defaultOrderBy = 'updated_at';
    protected string $defaultOrderDirection = 'desc';

    protected function getOrderBy(): string
    {
        return $this->defaultOrderBy;
    }

    protected function getOrderDirection(): string
    {
        return $this->defaultOrderDirection;
    }


    public function index()
    {
        $model = app($this->modelClass);
        // sorting
        $orderBy = $this->getOrderBy();
        $direction = $this->getOrderDirection();

        $relations = is_array($this->belongsTo)
            ? $this->belongsTo
            : (is_string($this->belongsTo)
                ? explode(",", $this->belongsTo)
                : []);

        // Query with or without relations
        $data = !empty($relations)
            ? $model
                ->with($relations)
                ->orderBy($orderBy, $direction)
                ->paginate(6)
            : $model->orderBy($orderBy, $direction)->paginate(6);

        // Pass plural field and resource name to the view
        $baseData = [
            $this->contextFieldPlural => $data,
            "resourceName" => $this->resourceName,
        ];

        return view(
            "admin.{$this->resourceName}.index",
            array_merge($baseData, $this->additionalIndexData())
        );
    }

    // Pass additional data for index view besides main model data
    protected function additionalIndexData(): array
    {
        return []; // Default: nothing extra
    }

    public function create()
    {
        return view(
            "admin.{$this->resourceName}.create",
            array_merge(
                [
                    "resourceName" => $this->resourceName,
                ],
                $this->additionalCreateData()
            )
        );
    }

    // Pass additional data for create view
    protected function additionalCreateData(): array
    {
        return []; // Default: nothing extra
    }

    public function edit($id)
    {
        $document = $this->modelClass::findOrFail($id);

        return view(
            "admin.{$this->resourceName}.edit",
            array_merge(
                [
                    $this->contextField => $document,
                    "resourceName" => $this->resourceName,
                ],
                $this->additionalEditData()
            )
        );
    }

    // Pass additional data for edit view
    protected function additionalEditData(): array
    {
        return []; // Default: nothing extra
    }

    public function destroy($id)
    {
        $document = $this->modelClass::findOrFail($id);

        if (!empty($this->fileFields)) {
            foreach ($this->fileFields as $field => $path) {
                $fileName = $document->{$field};
                if ($fileName) {
                    $filePath = public_path($path . $fileName);
                    if (File::exists($filePath)) {
                        File::delete($filePath);
                    }
                }
            }
        }

        $document->delete();

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "წარმატებით წაიშალა.");
    }

    protected function handleFileUpload(
        Request $request,
        string $fieldName,
        string $destinationPath,
        string $oldFile = null
    ) {
        $fileName = null;

        // Upload new file
        if ($request->file($fieldName)) {
            $file = $request->file($fieldName);
            $fileName =
                uniqid() .
                "-" .
                time() .
                "." .
                $file->getClientOriginalExtension();
            $file->move(public_path($destinationPath), $fileName);
        }

        // Delete old file if needed
        if ($request->file($fieldName) && $oldFile) {
            $oldFilePath = public_path($destinationPath . $oldFile);
            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }
        }

        return $fileName;
    }
}

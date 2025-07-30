<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\AppliesLocalScopes;
use App\Http\Controllers\Traits\HandlesFileUpload;
use Illuminate\Support\Facades\File;

abstract class CrudController extends Controller
{

    use AppliesLocalScopes, HandlesFileUpload;
    protected string $modelClass;
    protected string $contextField;
    protected string $contextFieldPlural;

    protected array $modelRelations = [];

    // If model have local scopes type name without "scope" prefix. In morel with prefix in here without
    protected array $localScopes = [];

    protected string $resourceName;

    // Remember field names should be the same as in database and in input form
    protected array $fileFields = []; // In case if user needs to upload multiple files in different paths (optional)

    // Apply default sorting and make it changeable if needed
    protected string $defaultOrderBy = 'updated_at';
    protected string $defaultOrderDirection = 'desc';

    protected int $perPage = 6;

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

        // Apply local scopes -> you now have a query builder
        $query = $this->applyLocalScopes($model);

        // Sorting
        $orderBy = $this->getOrderBy();
        $direction = $this->getOrderDirection();

        $relations = is_array($this->modelRelations)
            ? $this->modelRelations
            : (is_string($this->modelRelations)
                ? explode(",", $this->modelRelations)
                : []);

        // Use your original branching logic — just use $query instead of $model
        $data = !empty($relations)
            ? $query
                ->with($relations)
                ->orderBy($orderBy, $direction)
                ->paginate($this->perPage)
            : $query->orderBy($orderBy, $direction)->paginate($this->perPage);

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
}

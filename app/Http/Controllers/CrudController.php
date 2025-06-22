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

    protected string $resourceName;

    // Remember field names should be the same as in database and in input form
    protected array $fileFields = []; // In case if user needs to upload multiple files in different paths (optional)

    public function index()
    {
        $model = app($this->modelClass);
        return view("admin.{$this->resourceName}.index", [
            $this->resourceName => $model->orderBy("id", "DESC")->paginate(6),
            'resourceName' => $this->resourceName
        ]);
    }

    public function create()
    {
        return view("admin.{$this->resourceName}.create", );
    }

    public function edit($id)
    {
        $document = $this->modelClass::findOrFail($id);
        return view("admin.{$this->resourceName}.edit", [
            $this->contextField => $document,
        ]);
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
            $fileName = uniqid() . '-' . time() . '.' . $file->getClientOriginalExtension();
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

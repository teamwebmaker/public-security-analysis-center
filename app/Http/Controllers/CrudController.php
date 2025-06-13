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

    protected string $viewFolder;
    protected array $imageFields = []; // In case if user needs to upload multiple images in different paths (optional)

    public function index()
    {
        $model = app($this->modelClass);
        return view("admin.{$this->viewFolder}.index", [
            $this->viewFolder => $model->orderBy("id", "DESC")->paginate(6),
            "routeName" => Route::currentRouteName(),
        ]);
    }

    public function create()
    {
        return view("admin.{$this->viewFolder}.create",);
    }

    public function edit($id)
    {
        $document = $this->modelClass::findOrFail($id);
        return view("admin.{$this->viewFolder}.edit",[
            $this->contextField => $document,
        ]);
    }

    public function destroy($id)
    {
        $document = $this->modelClass::findOrFail($id);

        if (!empty($this->imageFields)) {
            foreach ($this->imageFields as $field => $path) {
                $imageName = $document->{$field};
                if ($imageName) {
                    $imagePath = public_path($path . $imageName);
                    if (File::exists($imagePath)) {
                        File::delete($imagePath);
                    }
                }
            }
        }

        $document->delete();

        return redirect()
            ->route("{$this->viewFolder}.index")
            ->with("success", "წარმატებით წაიშალა.");
    }

    protected function handleImageUpload(
        Request $request,
        $imageField,
        $imagePath,
        $oldImage = null
    ) {

        $imageName = null;
        
        // Upload image
        if ($request->file($imageField)) {
            $image = $request->file($imageField);
            $imageName = uniqid() . "-" . time() . "." . $image->extension();
            $image->move(public_path($imagePath), $imageName);
        }

        // Delete old image
        if ($request->file($imageField) && $oldImage) {
            $oldImagePath = public_path($imagePath . $oldImage);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
        }

        return $imageName;
    }
}

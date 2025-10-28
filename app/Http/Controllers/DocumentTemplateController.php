<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreDocumentTemplateRequest;
use App\Http\Requests\UpdateDocumentTemplateRequest;
use App\Models\DocumentTemplate;
use App\Models\User;
use Illuminate\Http\Request;

class DocumentTemplateController extends CrudController
{
    use SyncsRelations;
    protected string $modelClass = DocumentTemplate::class;
    protected string $contextField = "document_template";
    protected string $contextFieldPlural = "document_templates";
    protected string $resourceName = "document-templates";
    protected array $fileFields = ['document' => "documents/document-templates/"];


    public function additionalIndexData(): array
    {
        return $this->prepareDocumentTemplateAdditionalData();

    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentTemplateRequest $request)
    {
        $validatedData = $request->validated();


        $data = $this->prepareDocumentTemplateData($request, $validatedData);

        $document_template = $this->modelClass::create($data);

        $this->syncRelations($document_template, $data, [
            "users" => "worker_ids",
        ]);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "შაბლონი შეიქმნა წარმატებით");
    }

    protected function additionalCreateData(): array
    {
        return $this->prepareDocumentTemplateAdditionalData();
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentTemplateRequest $request, DocumentTemplate $DocumentTemplate)
    {
        $validatedData = $request->validated();
        $data = $this->prepareDocumentTemplateData($request, $validatedData, $DocumentTemplate);

        // Sync users (remove unchecked)
        $this->syncRelations($DocumentTemplate, $data, [
            "users" => "worker_ids",
        ]);

        $DocumentTemplate->update($data);

        return redirect()
            ->back()
            ->with("success", "შაბლონი განახლდა წარმატებით");
    }

    protected function additionalEditData(): array
    {
        return $this->prepareDocumentTemplateAdditionalData();
    }
    /**
     * Prepare documentTemplate data for storing or updating.
     */
    private function prepareDocumentTemplateData(
        Request $request,
        array $data,
        ?DocumentTemplate $document_template = null
    ): array {
        // Handle document upload
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $document_template) {
                $existing = $document_template?->$field;
                $file = $this->handleFileUpload(
                    $request,
                    $field,
                    $path,
                    $existing
                );
                return $file ? [$field => $file] : [];
            })
            ->toArray();


        return [
            ...$data,
            ...$files
        ];
    }


    private function prepareDocumentTemplateAdditionalData()
    {
        return [
            'workers' => User::select('id', 'full_name')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'worker');
                })
                ->get(),
        ];
    }
}

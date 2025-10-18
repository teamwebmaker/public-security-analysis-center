<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreInstructionRequest;
use App\Http\Requests\UpdateInstructionRequest;
use App\Models\Instruction;
use App\Models\User;
use Illuminate\Http\Request;

class InstructionController extends CrudController
{
    use SyncsRelations;
    protected string $modelClass = Instruction::class;
    protected string $contextField = "instruction";
    protected string $contextFieldPlural = "instructions";
    protected string $resourceName = "instructions";
    protected array $fileFields = ['document' => "documents/instructions/"];


    public function additionalIndexData(): array
    {
        return $this->prepareInstructionAdditionalData();

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInstructionRequest $request)
    {
        $validatedData = $request->validated();


        $data = $this->prepareInstructionData($request, $validatedData);

        $instruction = $this->modelClass::create($data);

        $this->syncRelations($instruction, $data, [
            "users" => "worker_ids",
        ]);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "ინსტრუქტაჟი შეიქმნა წარმატებით");
    }


    protected function additionalCreateData(): array
    {
        return $this->prepareInstructionAdditionalData();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstructionRequest $request, Instruction $instruction)
    {
        $validatedData = $request->validated();
        $data = $this->prepareInstructionData($request, $validatedData, $instruction);

        // Sync users (remove unchecked)
        $this->syncRelations($instruction, $data, [
            "users" => "worker_ids",
        ]);

        $instruction->update($data);

        return redirect()
            ->back()
            ->with("success", "ინსტრუქტაჟი განახლდა წარმატებით");
    }
    protected function additionalEditData(): array
    {
        return $this->prepareInstructionAdditionalData();
    }


    private function prepareInstructionAdditionalData()
    {
        return [
            'workers' => User::select('id', 'full_name')
                ->whereHas('role', function ($query) {
                    $query->where('name', 'worker');
                })
                ->get(),
        ];
    }


    /**
     * Prepare Instruction data for storing or updating.
     */
    private function prepareInstructionData(
        Request $request,
        array $data,
        ?Instruction $instruction = null
    ): array {
        // Handle document upload
        $files = collect($this->fileFields)
            ->mapWithKeys(function ($path, $field) use ($request, $instruction) {
                $existing = $instruction?->$field;
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

}

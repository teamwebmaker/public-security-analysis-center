<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstructionRequest;
use App\Http\Requests\UpdateInstructionRequest;
use App\Models\Instruction;
use App\Models\User;
use App\Services\Instructions\InstructionDocumentStorage;
use App\Services\Instructions\InstructionFilterService;
use App\Services\Instructions\InstructionManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class InstructionController extends CrudController
{
    protected string $modelClass = Instruction::class;

    protected string $contextField = 'instruction';

    protected string $contextFieldPlural = 'instructions';

    protected string $resourceName = 'instructions';

    public function __construct(
        private InstructionManager $instructionManager,
        private InstructionDocumentStorage $documentStorage,
        private InstructionFilterService $filters
    ) {}

    public function index(Request $request): View
    {
        $query = Instruction::query()->with(['users:id,full_name', 'publicShare']);
        $this->filters->apply($query, $request, true);

        return view('admin.instructions.index', [
            'instructions' => $query
                ->orderByDesc('updated_at')
                ->paginate($this->perPage)
                ->appends($request->query()),
            'workers' => $this->workers(),
            'resourceName' => $this->resourceName,
        ]);
    }

    public function store(StoreInstructionRequest $request): RedirectResponse
    {
        $this->instructionManager->create($request->validated(), $request->user());

        return redirect()
            ->route('instructions.index')
            ->with('success', 'ინსტრუქტაჟი შეიქმნა წარმატებით');
    }

    protected function additionalCreateData(): array
    {
        return ['workers' => $this->workers()];
    }

    public function edit($id): View
    {
        $instruction = Instruction::query()
            ->with(['users:id,full_name', 'publicShare'])
            ->findOrFail($id);

        return view('admin.instructions.edit', [
            'instruction' => $instruction,
            'workers' => $this->workers(),
            'resourceName' => $this->resourceName,
        ]);
    }

    public function update(
        UpdateInstructionRequest $request,
        Instruction $instruction
    ): RedirectResponse {
        $this->instructionManager->update($instruction, $request->validated(), $request->user());

        return redirect()
            ->back()
            ->with('success', 'ინსტრუქტაჟი განახლდა წარმატებით');
    }

    public function destroy($id): RedirectResponse
    {
        $instruction = Instruction::query()->findOrFail($id);
        $this->instructionManager->delete($instruction);

        return redirect()
            ->route('instructions.index')
            ->with('success', 'წარმატებით წაიშალა.');
    }

    public function document(Instruction $instruction): BinaryFileResponse
    {
        $this->authorize('view', $instruction);

        $response = response()->file(
            $this->documentStorage->absolutePath($instruction),
            [
                'Content-Type' => $instruction->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $instruction->document_original_name ?: basename($instruction->document)
        );

        return $response;
    }

    public function downloadDocument(Instruction $instruction): BinaryFileResponse
    {
        $this->authorize('view', $instruction);

        return response()->download(
            $this->documentStorage->absolutePath($instruction),
            $instruction->document_original_name ?: basename($instruction->document),
            [
                'Content-Type' => $instruction->document_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    private function workers()
    {
        return User::query()
            ->select('id', 'full_name')
            ->whereHas('role', fn ($query) => $query->where('name', 'worker'))
            ->orderBy('full_name')
            ->get();
    }
}

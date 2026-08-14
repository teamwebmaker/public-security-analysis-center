<?php

namespace App\Services\Instructions;

use App\Models\Instruction;
use App\Models\User;
use App\Services\PublicSharing\PublicShareService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class InstructionManager
{
    public function __construct(
        private InstructionDocumentStorage $documents,
        private PublicShareService $publicShares
    ) {}

    public function create(array $data, User $creator): Instruction
    {
        $storedDocument = $this->documents->store($data['document']);

        try {
            return DB::transaction(function () use ($data, $creator, $storedDocument) {
                $workerIds = Arr::pull($data, 'worker_ids', []);
                unset($data['document']);

                $instruction = Instruction::create([
                    ...$data,
                    'document' => $storedDocument['path'],
                    'document_original_name' => $storedDocument['original_name'],
                    'document_mime_type' => $storedDocument['mime_type'],
                ]);
                $instruction->users()->sync($workerIds);

                if ($instruction->isPublic()) {
                    $this->publicShares->enable($instruction, $creator);
                }

                return $instruction->load(['users', 'publicShare']);
            });
        } catch (Throwable $e) {
            $this->documents->delete($storedDocument['path']);
            throw $e;
        }
    }

    public function update(Instruction $instruction, array $data, User $actor): Instruction
    {
        $newDocument = isset($data['document']) && $data['document'] instanceof UploadedFile
            ? $this->documents->store($data['document'])
            : null;
        $oldDocumentPath = $instruction->document;

        try {
            $updated = DB::transaction(function () use ($instruction, $data, $actor, $newDocument) {
                $workerIds = Arr::pull($data, 'worker_ids', []);
                unset($data['document']);

                if ($newDocument !== null) {
                    $data['document'] = $newDocument['path'];
                    $data['document_original_name'] = $newDocument['original_name'];
                    $data['document_mime_type'] = $newDocument['mime_type'];
                }

                $instruction->update($data);
                $instruction->users()->sync($workerIds);

                if ($instruction->isPublic()) {
                    $this->publicShares->enable($instruction, $actor);
                } else {
                    $this->publicShares->disable($instruction);
                }

                return $instruction->fresh(['users', 'publicShare']);
            });
        } catch (Throwable $e) {
            if ($newDocument !== null) {
                $this->documents->delete($newDocument['path']);
            }
            throw $e;
        }

        if ($newDocument !== null && $oldDocumentPath !== $newDocument['path']) {
            $this->documents->delete($oldDocumentPath);
        }

        return $updated;
    }

    public function delete(Instruction $instruction): void
    {
        $documentPath = $instruction->document;

        DB::transaction(function () use ($instruction) {
            $instruction->publicShares()->delete();
            $instruction->delete();
        });

        $this->documents->delete($documentPath);
    }
}

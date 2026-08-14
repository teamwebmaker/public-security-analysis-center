<?php

namespace App\Services\Instructions;

use App\Models\Instruction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class InstructionDocumentStorage
{
    public function store(UploadedFile $document): array
    {
        $extension = strtolower($document->getClientOriginalExtension());
        $fileName = Str::uuid().($extension !== '' ? ".{$extension}" : '');
        $path = $document->storeAs('instructions', $fileName, 'local');

        if ($path === false) {
            throw new RuntimeException('Instruction document could not be stored.');
        }

        return [
            'path' => $path,
            'original_name' => $document->getClientOriginalName(),
            'mime_type' => $document->getMimeType(),
        ];
    }

    public function locate(Instruction $instruction): array
    {
        if (Storage::disk('local')->exists($instruction->document)) {
            return ['disk' => 'local', 'path' => $instruction->document];
        }

        $legacyPath = normalize_public_upload_path(
            $instruction->document,
            'documents/instructions'
        );

        if ($legacyPath
            && ! filter_var($legacyPath, FILTER_VALIDATE_URL)
            && Storage::disk('public')->exists($legacyPath)) {
            return ['disk' => 'public', 'path' => $legacyPath];
        }

        abort(404);
    }

    public function absolutePath(Instruction $instruction): string
    {
        $location = $this->locate($instruction);

        return Storage::disk($location['disk'])->path($location['path']);
    }

    public function delete(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('local')->delete($path);

        $legacyPath = normalize_public_upload_path($path, 'documents/instructions');
        if ($legacyPath && ! filter_var($legacyPath, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($legacyPath);
        }
    }
}

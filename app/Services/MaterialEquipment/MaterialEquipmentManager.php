<?php

namespace App\Services\MaterialEquipment;

use App\Models\MaterialEquipment;
use App\Models\User;
use App\Services\PublicSharing\PublicShareService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class MaterialEquipmentManager
{
    public function __construct(private PublicShareService $publicShares) {}

    public function create(array $data, User $creator): MaterialEquipment
    {
        $storedPath = $this->storeDocument($data['document']);

        try {
            return DB::transaction(function () use ($data, $creator, $storedPath) {
                /** @var UploadedFile $document */
                $document = $data['document'];
                $materialEquipment = MaterialEquipment::create([
                    'title' => trim($data['title']),
                    'branch_id' => $data['branch_id'],
                    'created_by_user_id' => $creator->id,
                    'document_path' => $storedPath,
                    'document_original_name' => $document->getClientOriginalName(),
                    'document_mime_type' => $document->getMimeType(),
                    'document_visibility' => $data['document_visibility'],
                ]);

                $this->createSignatures($materialEquipment, collect($data['signer_user_ids']));

                if ($materialEquipment->isPublic()) {
                    $this->publicShares->enable($materialEquipment, $creator);
                }

                return $materialEquipment->load(['branch.company', 'signatures.user.role', 'publicShare']);
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($storedPath);
            throw $e;
        }
    }

    public function update(MaterialEquipment $materialEquipment, array $data, User $actor): MaterialEquipment
    {
        return DB::transaction(function () use ($materialEquipment, $data, $actor) {
            $materialEquipment->update([
                'title' => trim($data['title']),
                'document_visibility' => $data['document_visibility'],
            ]);

            $this->syncSignatures($materialEquipment, collect($data['signer_user_ids']));

            if ($materialEquipment->isPublic()) {
                $this->publicShares->enable($materialEquipment, $actor);
            } else {
                $this->publicShares->disable($materialEquipment);
            }

            return $materialEquipment->fresh(['branch.company', 'signatures.user.role', 'publicShare']);
        });
    }

    public function delete(MaterialEquipment $materialEquipment): void
    {
        $path = $materialEquipment->document_path;

        DB::transaction(function () use ($materialEquipment) {
            $materialEquipment->publicShares()->delete();
            $materialEquipment->delete();
        });

        Storage::disk('local')->delete($path);
    }

    private function createSignatures(MaterialEquipment $materialEquipment, Collection $userIds): void
    {
        $userIds->map(fn ($id) => (int) $id)
            ->unique()
            ->each(fn (int $userId) => $materialEquipment->signatures()->create([
                'user_id' => $userId,
                'signed_at' => now(),
            ]));
    }

    private function syncSignatures(MaterialEquipment $materialEquipment, Collection $selectedIds): void
    {
        $selectedIds = $selectedIds->map(fn ($id) => (int) $id)->unique()->values();
        $existingIds = $materialEquipment->signatures()->pluck('user_id');

        $materialEquipment->signatures()->whereNotIn('user_id', $selectedIds)->delete();

        $selectedIds->diff($existingIds)->each(
            fn (int $userId) => $materialEquipment->signatures()->create([
                'user_id' => $userId,
                'signed_at' => now(),
            ])
        );
    }

    private function storeDocument(UploadedFile $document): string
    {
        $extension = strtolower($document->getClientOriginalExtension());
        $fileName = Str::uuid().($extension !== '' ? ".{$extension}" : '');
        $path = $document->storeAs('material-equipments', $fileName, 'local');

        if ($path === false) {
            throw new RuntimeException('Material equipment document could not be stored.');
        }

        return $path;
    }
}

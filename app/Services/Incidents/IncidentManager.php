<?php

namespace App\Services\Incidents;

use App\Models\Incident;
use App\Models\IncidentExternalParticipant;
use App\Models\IncidentUserParticipant;
use App\Models\User;
use App\Services\PublicSharing\PublicShareService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class IncidentManager
{
    public function __construct(private PublicShareService $publicShares)
    {
    }

    public function create(array $data, User $creator): array
    {
        $storedPath = $this->storeDocument($data['document']);

        try {
            return DB::transaction(function () use ($data, $creator, $storedPath) {
                /** @var UploadedFile $document */
                $document = $data['document'];
                $incident = Incident::create([
                    'title' => trim($data['title']),
                    'branch_id' => $data['branch_id'],
                    'created_by_user_id' => $creator->id,
                    'document_path' => $storedPath,
                    'document_original_name' => $document->getClientOriginalName(),
                    'document_mime_type' => $document->getMimeType(),
                    'document_visibility' => $data['document_visibility'],
                ]);

                $userParticipants = $this->createUserParticipants(
                    $incident,
                    collect($data['user_participant_ids'])->map(fn($id) => (int) $id)
                );
                $externalParticipants = $this->createExternalParticipants(
                    $incident,
                    $data['external_participants'] ?? []
                );

                if ($incident->isPublic()) {
                    $this->publicShares->enable($incident, $creator);
                }

                return [
                    'incident' => $incident->load(['branch.company', 'publicShare']),
                    'new_user_participants' => $userParticipants,
                    'new_external_participants' => $externalParticipants,
                    'became_public' => false,
                ];
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($storedPath);
            throw $e;
        }
    }

    public function update(Incident $incident, array $data, User $actor): array
    {
        $newPath = isset($data['document']) && $data['document'] instanceof UploadedFile
            ? $this->storeDocument($data['document'])
            : null;
        $oldPath = $incident->document_path;
        $wasPublic = $incident->isPublic();

        try {
            $result = DB::transaction(function () use ($incident, $data, $actor, $newPath, $wasPublic) {
                $incident->title = trim($data['title']);
                $incident->document_visibility = $data['document_visibility'];

                if ($newPath !== null) {
                    /** @var UploadedFile $document */
                    $document = $data['document'];
                    $incident->document_path = $newPath;
                    $incident->document_original_name = $document->getClientOriginalName();
                    $incident->document_mime_type = $document->getMimeType();
                }

                $incident->save();

                $newUserParticipants = $this->syncUserParticipants(
                    $incident,
                    collect($data['user_participant_ids'])->map(fn($id) => (int) $id)
                );
                $newExternalParticipants = $this->syncExternalParticipants(
                    $incident,
                    $data['external_participants'] ?? []
                );

                if ($incident->isPublic()) {
                    $this->publicShares->enable($incident, $actor);
                } else {
                    $this->publicShares->disable($incident);
                }

                return [
                    'incident' => $incident->fresh(['branch.company', 'publicShare']),
                    'new_user_participants' => $newUserParticipants,
                    'new_external_participants' => $newExternalParticipants,
                    'became_public' => !$wasPublic && $incident->isPublic(),
                ];
            });
        } catch (Throwable $e) {
            if ($newPath !== null) {
                Storage::disk('local')->delete($newPath);
            }
            throw $e;
        }

        if ($newPath !== null && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return $result;
    }

    private function createUserParticipants(Incident $incident, Collection $userIds): Collection
    {
        return $userIds->unique()->values()->map(
            fn(int $userId) => $incident->userParticipants()->create(['user_id' => $userId])
        );
    }

    private function syncUserParticipants(Incident $incident, Collection $selectedIds): Collection
    {
        $selectedIds = $selectedIds->unique()->values();
        $existing = $incident->userParticipants()->get()->keyBy('user_id');
        $signedIds = $existing->filter(fn(IncidentUserParticipant $participant) => $participant->signed_at !== null)
            ->keys();
        $finalIds = $selectedIds->merge($signedIds)->unique()->values();

        $incident->userParticipants()
            ->whereNull('signed_at')
            ->whereNotIn('user_id', $finalIds)
            ->delete();

        return $finalIds
            ->reject(fn(int $userId) => $existing->has($userId))
            ->map(fn(int $userId) => $incident->userParticipants()->create(['user_id' => $userId]))
            ->values();
    }

    private function createExternalParticipants(Incident $incident, array $participants): Collection
    {
        return collect($participants)
            ->filter(fn(array $participant) => trim((string) ($participant['full_name'] ?? '')) !== '')
            ->map(fn(array $participant) => $incident->externalParticipants()->create([
                'full_name' => trim($participant['full_name']),
                'phone' => $this->nullableTrim($participant['phone'] ?? null),
            ]))
            ->values();
    }

    private function syncExternalParticipants(Incident $incident, array $participants): Collection
    {
        $keptIds = collect();
        $created = collect();

        foreach ($participants as $participantData) {
            $name = trim((string) ($participantData['full_name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $participantId = (int) ($participantData['id'] ?? 0);
            if ($participantId > 0) {
                $participant = $incident->externalParticipants()->findOrFail($participantId);
                if ($participant->signed_at === null) {
                    $participant->update([
                        'full_name' => $name,
                        'phone' => $this->nullableTrim($participantData['phone'] ?? null),
                    ]);
                }
                $keptIds->push($participant->id);
                continue;
            }

            $newParticipant = $incident->externalParticipants()->create([
                'full_name' => $name,
                'phone' => $this->nullableTrim($participantData['phone'] ?? null),
            ]);
            $keptIds->push($newParticipant->id);
            $created->push($newParticipant);
        }

        $incident->externalParticipants()
            ->whereNull('signed_at')
            ->when($keptIds->isNotEmpty(), fn($query) => $query->whereNotIn('id', $keptIds))
            ->delete();

        return $created;
    }

    private function storeDocument(UploadedFile $document): string
    {
        $extension = strtolower($document->getClientOriginalExtension());
        $fileName = Str::uuid() . ($extension !== '' ? ".{$extension}" : '');
        $path = $document->storeAs('incidents', $fileName, 'local');

        if ($path === false) {
            throw new RuntimeException('Incident document could not be stored.');
        }

        return $path;
    }

    private function nullableTrim(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}

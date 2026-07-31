<?php

namespace App\Services\Orders;

use App\Models\Order;
use App\Models\OrderUserParticipant;
use App\Models\User;
use App\Services\PublicSharing\PublicShareService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class OrderManager
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
                $order = Order::create([
                    'title' => trim($data['title']),
                    'branch_id' => $data['branch_id'],
                    'created_by_user_id' => $creator->id,
                    'document_path' => $storedPath,
                    'document_original_name' => $document->getClientOriginalName(),
                    'document_mime_type' => $document->getMimeType(),
                    'document_visibility' => $data['document_visibility'],
                ]);

                $participants = $this->createParticipants(
                    $order,
                    collect($data['user_participant_ids'])->map(fn($id) => (int) $id)
                );

                if ($order->isPublic()) {
                    $this->publicShares->enable($order, $creator);
                }

                return [
                    'order' => $order->load(['branch.company', 'publicShare']),
                    'new_participants' => $participants,
                ];
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($storedPath);
            throw $e;
        }
    }

    public function update(Order $order, array $data, User $actor): array
    {
        $newPath = isset($data['document']) && $data['document'] instanceof UploadedFile
            ? $this->storeDocument($data['document'])
            : null;
        $oldPath = $order->document_path;

        try {
            $result = DB::transaction(function () use ($order, $data, $actor, $newPath) {
                $order->title = trim($data['title']);
                $order->document_visibility = $data['document_visibility'];

                if ($newPath !== null) {
                    /** @var UploadedFile $document */
                    $document = $data['document'];
                    $order->document_path = $newPath;
                    $order->document_original_name = $document->getClientOriginalName();
                    $order->document_mime_type = $document->getMimeType();
                }

                $order->save();
                $newParticipants = $this->syncParticipants(
                    $order,
                    collect($data['user_participant_ids'])->map(fn($id) => (int) $id)
                );

                if ($order->isPublic()) {
                    $this->publicShares->enable($order, $actor);
                } else {
                    $this->publicShares->disable($order);
                }

                return [
                    'order' => $order->fresh(['branch.company', 'publicShare']),
                    'new_participants' => $newParticipants,
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

    private function createParticipants(Order $order, Collection $userIds): Collection
    {
        return $userIds->unique()->values()->map(
            fn(int $userId) => $order->userParticipants()->create(['user_id' => $userId])
        );
    }

    private function syncParticipants(Order $order, Collection $selectedIds): Collection
    {
        $selectedIds = $selectedIds->unique()->values();
        $existing = $order->userParticipants()->get()->keyBy('user_id');
        $signedIds = $existing
            ->filter(fn(OrderUserParticipant $participant) => $participant->signed_at !== null)
            ->keys();
        $finalIds = $selectedIds->merge($signedIds)->unique()->values();

        $order->userParticipants()
            ->whereNull('signed_at')
            ->whereNotIn('user_id', $finalIds)
            ->delete();

        return $finalIds
            ->reject(fn(int $userId) => $existing->has($userId))
            ->map(fn(int $userId) => $order->userParticipants()->create(['user_id' => $userId]))
            ->values();
    }

    private function storeDocument(UploadedFile $document): string
    {
        $extension = strtolower($document->getClientOriginalExtension());
        $fileName = Str::uuid() . ($extension !== '' ? ".{$extension}" : '');
        $path = $document->storeAs('orders', $fileName, 'local');

        if ($path === false) {
            throw new RuntimeException('Order document could not be stored.');
        }

        return $path;
    }
}

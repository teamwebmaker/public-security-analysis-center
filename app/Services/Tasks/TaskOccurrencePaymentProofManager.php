<?php

namespace App\Services\Tasks;

use App\Models\TaskOccurrence;
use App\Models\User;
use App\Services\Messages\MessageStoreService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class TaskOccurrencePaymentProofManager
{
    public function __construct(private MessageStoreService $messageStoreService)
    {
    }

    public function store(TaskOccurrence $taskOccurrence, UploadedFile $file, User $uploader): TaskOccurrence
    {
        $newPath = $file->store('documents/task-payment-proofs', 'local');
        if (! $newPath) {
            throw new RuntimeException('Payment proof could not be stored.');
        }

        $oldPath = $taskOccurrence->payment_proof_path;

        try {
            DB::transaction(function () use ($taskOccurrence, $file, $uploader, $newPath) {
                $uploadedAt = now();

                $taskOccurrence->update([
                    'payment_status' => 'pending',
                    'payment_proof_path' => $newPath,
                    'payment_proof_original_name' => $file->getClientOriginalName(),
                    'payment_proof_mime_type' => $file->getMimeType(),
                    'payment_proof_uploaded_by_user_id' => $uploader->id,
                    'payment_proof_uploaded_at' => $uploadedAt,
                ]);

                $this->messageStoreService->createAndDispatch([
                    'source' => 'system',
                    'type' => 'payment',
                    'full_name' => 'system',
                    'phone' => 'undefined',
                    'email' => 'undefined',
                    'subject' => 'გადახდის დამადასტურებელი დოკუმენტი აიტვირთა',
                    'message' => $this->systemMessage($taskOccurrence, $uploader, $uploadedAt),
                ]);
            });
        } catch (Throwable $e) {
            Storage::disk('local')->delete($newPath);
            throw $e;
        }

        if ($oldPath && $oldPath !== $newPath) {
            Storage::disk('local')->delete($oldPath);
        }

        return $taskOccurrence->fresh(['paymentProofUploader']);
    }

    private function systemMessage(TaskOccurrence $taskOccurrence, User $uploader, $uploadedAt): string
    {
        $uploadedAtTbilisi = $uploadedAt->copy()->setTimezone('Asia/Tbilisi')->format('d.m.Y H:i');
        $dueDate = $taskOccurrence->due_date?->format('d.m.Y') ?? '—';

        return "გადახდის დამადასტურებელი დოკუმენტი აიტვირთა.\n"
            ."ვინ: {$uploader->full_name}\n"
            ."როდის: {$uploadedAtTbilisi}\n"
            ."დანიშნულება: საქმე #{$taskOccurrence->id}-ის გადახდა\n"
            ."ფილიალი: {$taskOccurrence->branch_name_snapshot}\n"
            ."სერვისი: {$taskOccurrence->service_name_snapshot}\n"
            ."გადახდის ბოლო ვადა: {$dueDate}\n"
            .'სტატუსი: ადმინისტრატორის დადასტურების მოლოდინში';
    }
}

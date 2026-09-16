<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskOccurrencePaymentProofRequest;
use App\Models\TaskOccurrence;
use App\Services\Tasks\TaskOccurrencePaymentProofManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class TaskOccurrencePaymentProofController extends Controller
{
    public function store(
        StoreTaskOccurrencePaymentProofRequest $request,
        TaskOccurrence $taskOccurrence,
        TaskOccurrencePaymentProofManager $manager
    ): RedirectResponse {
        $manager->store(
            $taskOccurrence,
            $request->file('payment_proof'),
            $request->user()
        );

        return back()->with('success', 'გადახდის დამადასტურებელი დოკუმენტი წარმატებით აიტვირთა.');
    }

    public function show(TaskOccurrence $taskOccurrence): BinaryFileResponse
    {
        $this->authorize('viewPaymentProof', $taskOccurrence);
        $this->ensureProofExists($taskOccurrence);

        $response = response()->file(
            Storage::disk('local')->path($taskOccurrence->payment_proof_path),
            [
                'Content-Type' => $taskOccurrence->payment_proof_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            $taskOccurrence->payment_proof_original_name
        );

        return $response;
    }

    public function download(TaskOccurrence $taskOccurrence): BinaryFileResponse
    {
        $this->authorize('viewPaymentProof', $taskOccurrence);
        $this->ensureProofExists($taskOccurrence);

        return response()->download(
            Storage::disk('local')->path($taskOccurrence->payment_proof_path),
            $taskOccurrence->payment_proof_original_name,
            [
                'Content-Type' => $taskOccurrence->payment_proof_mime_type ?: 'application/octet-stream',
                'Cache-Control' => 'private, no-store',
            ]
        );
    }

    private function ensureProofExists(TaskOccurrence $taskOccurrence): void
    {
        abort_unless(
            $taskOccurrence->payment_proof_path
                && Storage::disk('local')->exists($taskOccurrence->payment_proof_path),
            404
        );
    }
}

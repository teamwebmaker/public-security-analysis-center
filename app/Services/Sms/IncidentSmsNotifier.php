<?php

namespace App\Services\Sms;

use App\Models\Incident;
use App\Models\SmsLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Throwable;

class IncidentSmsNotifier
{
    public function __construct(private SmsLogService $smsLogService)
    {
    }

    public function notifyCreated(
        Incident $incident,
        ?Collection $userParticipants = null,
        ?Collection $externalParticipants = null
    ): void {
        $incident->loadMissing(['branch', 'publicShare']);
        $userParticipants ??= $incident->userParticipants()->with('user.role')->get();
        $externalParticipants ??= $incident->externalParticipants()->get();

        foreach ($userParticipants as $participant) {
            $participant->loadMissing('user.role');
            $user = $participant->user;
            if (!$user || trim((string) $user->phone) === '') {
                continue;
            }

            $this->send(
                $user->phone,
                $this->systemUserMessage($incident),
                'incident_created',
                $incident->id,
                $user->getRoleName()
            );
        }

        foreach ($externalParticipants as $participant) {
            if (trim((string) $participant->phone) === '') {
                continue;
            }

            $this->send(
                $participant->phone,
                $this->externalPersonMessage($incident),
                'incident_created',
                $incident->id,
                'external_person'
            );
        }
    }

    public function notifyPublicLinkAvailable(Incident $incident): void
    {
        $incident->loadMissing(['branch', 'publicShare']);

        foreach ($incident->externalParticipants()->whereNotNull('phone')->get() as $participant) {
            $this->send(
                $participant->phone,
                $this->externalPersonMessage($incident),
                'incident_shared',
                $incident->id,
                'external_person'
            );
        }
    }

    private function systemUserMessage(Incident $incident): string
    {
        return "⚠️ ახალი ინციდენტი\n"
            . "ინციდენტი: {$incident->title}\n"
            . "ფილიალი: {$incident->branch->name}\n"
            . 'გთხოვთ, შეხვიდეთ სისტემაში და დაადასტუროთ დოკუმენტი.';
    }

    private function externalPersonMessage(Incident $incident): string
    {
        $message = "⚠️ ახალი ინციდენტი\n"
            . "ინციდენტი: {$incident->title}\n"
            . "ფილიალი: {$incident->branch->name}";

        if ($incident->isPublic() && $incident->publicShare?->isUsable()) {
            $message .= "\nდოკუმენტი: " . route('public-shares.show', $incident->publicShare->token);
        }

        return $message;
    }

    private function send(
        string $destination,
        string $message,
        string $eventType,
        int $incidentId,
        string $recipientType
    ): void {
        try {
            if ($this->smsLogService->alreadySent($destination, $eventType, $incidentId, $recipientType)) {
                return;
            }

            $this->smsLogService->sendEventNotification(
                $destination,
                $message,
                $eventType,
                $incidentId,
                $recipientType,
                SmsLog::smsnoTypeNumber('information') ?? 2
            );
        } catch (Throwable $e) {
            Log::error('Incident SMS notification failed', [
                'incident_id' => $incidentId,
                'event_type' => $eventType,
                'recipient_type' => $recipientType,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

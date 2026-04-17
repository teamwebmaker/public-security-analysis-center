<?php

namespace App\Services\Sms;

use App\Models\TaskOccurrence;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;

class ResponsiblePersonTaskSmsNotifier
{
    public function __construct(
        private SmsLogService $smsLogService,
        private ResponsiblePersonSmsSender $smsSender
    ) {
    }

    public function notifyTaskAssigned(TaskOccurrence $occurrence): array
    {
        return $this->notifyForOccurrences([$occurrence], 'task_assigned');
    }

    public function notifyTaskFinished(TaskOccurrence $occurrence): array
    {
        return $this->notifyForOccurrences([$occurrence], 'task_finished');
    }

    /**
     * @param array<int, int|string> $occurrenceIds
     */
    public function notifyTaskAssignedForOccurrenceIds(array $occurrenceIds): array
    {
        $occurrenceIds = array_values(array_unique(array_filter(array_map('intval', $occurrenceIds))));
        if (empty($occurrenceIds)) {
            return $this->emptySummary();
        }

        $occurrences = new EloquentCollection();

        foreach (array_chunk($occurrenceIds, 500) as $idChunk) {
            $chunk = TaskOccurrence::query()
                ->withoutGlobalScope('visible')
                ->whereIn('id', $idChunk)
                ->with('task.branch.users.services')
                ->get();

            $occurrences = $occurrences->merge($chunk);
        }

        return $this->notifyForOccurrences($occurrences->all(), 'task_assigned');
    }

    /**
     * @param array<int, mixed> $occurrences
     */
    private function notifyForOccurrences(array $occurrences, string $eventType): array
    {
        $occurrenceCollection = $this->toOccurrenceCollection($occurrences);
        if ($occurrenceCollection->isEmpty()) {
            return $this->emptySummary();
        }

        $byUser = [];
        $metaByOccurrenceId = [];

        foreach ($occurrenceCollection as $occurrence) {
            $metaByOccurrenceId[(int) $occurrence->id] = [
                'branch_name' => trim((string) ($occurrence->branch_name_snapshot ?? '—')),
                'service_name' => trim((string) ($occurrence->service_name_snapshot ?? '—')),
                'due_date' => $occurrence->due_date?->format('d.m.Y') ?? '—',
            ];

            $users = $occurrence->task?->branch?->users ?? collect();
            if ($users->isEmpty()) {
                Log::warning('Responsible-person task SMS skipped: no recipients found for branch.', [
                    'event_type' => $eventType,
                    'occurrence_id' => $occurrence->id,
                    'branch_id' => $occurrence->task?->branch?->id,
                ]);
                continue;
            }

            foreach ($users as $user) {
                $byUser[$user->id]['user'] = $user;
                $byUser[$user->id]['occurrence_ids'][] = $occurrence->id;
                $byUser[$user->id]['occurrence_service_ids'][$occurrence->id] = $occurrence->service_id_snapshot;
            }
        }

        if (empty($byUser)) {
            return $this->emptySummary();
        }

        return $this->smsSender->sendAggregatedSmsToResponsiblePersons(
            $byUser,
            $this->smsLogService,
            $eventType,
            fn(array $occurrenceIds) => $this->buildMessage($eventType, $occurrenceIds, $metaByOccurrenceId),
            $eventType === 'task_assigned'
                ? 'Sending responsible-person task assignment SMS'
                : 'Sending responsible-person task completion SMS'
        );
    }

    /**
     * @param array<int, array{branch_name: string, service_name: string, due_date: string}> $metaByOccurrenceId
     * @param array<int, int|string> $occurrenceIds
     */
    private function buildMessage(string $eventType, array $occurrenceIds, array $metaByOccurrenceId): string
    {
        $list = $this->buildOccurrenceList($occurrenceIds, $metaByOccurrenceId, $eventType === 'task_assigned');

        if ($eventType === 'task_finished') {
            return "✅ სამუშაო დასრულდა თქვენს ფილიალში\n"
                . "სამუშაოები: {$list}";
        }

        return "📌 ახალი სამუშაო დაენიშნა თქვენს ფილიალს\n"
            . "სამუშაოები: {$list}";
    }

    /**
     * @param array<int, array{branch_name: string, service_name: string, due_date: string}> $metaByOccurrenceId
     * @param array<int, int|string> $occurrenceIds
     */
    private function buildOccurrenceList(array $occurrenceIds, array $metaByOccurrenceId, bool $includeDueDate): string
    {
        $occurrenceIds = array_values(array_unique(array_filter(array_map('intval', $occurrenceIds))));
        if (empty($occurrenceIds)) {
            return '—';
        }

        $visibleLimit = 5;
        $visibleIds = array_slice($occurrenceIds, 0, $visibleLimit);
        $hiddenCount = count($occurrenceIds) - count($visibleIds);

        $items = array_map(function (int $occurrenceId) use ($metaByOccurrenceId, $includeDueDate): string {
            $meta = $metaByOccurrenceId[$occurrenceId] ?? [
                'branch_name' => '—',
                'service_name' => '—',
                'due_date' => '—',
            ];

            $suffix = $includeDueDate ? ", {$meta['due_date']}" : '';

            return "#{$occurrenceId} ({$meta['branch_name']} / {$meta['service_name']}{$suffix})";
        }, $visibleIds);

        $list = implode(', ', $items);

        if ($hiddenCount > 0) {
            $list .= " +{$hiddenCount}";
        }

        return $list;
    }

    /**
     * @param array<int, mixed> $occurrences
     */
    private function toOccurrenceCollection(array $occurrences): EloquentCollection
    {
        $items = array_values(array_filter(
            $occurrences,
            fn($item) => $item instanceof TaskOccurrence
        ));

        $collection = new EloquentCollection($items);
        if ($collection->isNotEmpty()) {
            $collection->loadMissing('task.branch.users.services');
        }

        return $collection;
    }

    private function emptySummary(): array
    {
        return [
            'sent' => [],
            'skipped' => [
                'missing_phone' => [],
                'not_authorized' => [],
            ],
        ];
    }
}

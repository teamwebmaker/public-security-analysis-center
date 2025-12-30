<?php

namespace App\Presenters;

use App\Models\Branch;
use App\Models\Task;
use App\Models\TaskOccurrence;
use App\Models\SmsLog;
use Illuminate\Database\Eloquent\Model;

class TableRowDataPresenter
{
   /**
    * Build an admin task row for task index tables.
    */
   public static function adminTaskRow(Task $task): array
   {
      $occurrence = $task->latestOccurrenceWithoutVisibility;

      return [
         'id' => $task->id,
         'visibility' => self::badge(
            $task->visibility ? 'ხილული' : 'დამალული',
            $task->visibility ? 'success' : 'danger'
         ),
         'worker' => self::formatTaskWorkers($task),
         'branch' => self::link(
            $task->branch,
            'branches.index',
            $task->branch?->name ?? $task->branch_name_snapshot ?? '---',
            $task->branch_name_snapshot ?? '---'
         ),
         'service' => self::link(
            $task->service,
            'services.index',
            $task->service?->title->ka
            ?? $task->service?->title->en
            ?? $task->service_name_snapshot
            ?? 'უცნობი',
            $task->service_name_snapshot ?? 'უცნობი'
         ),
         'occ_status' => $occurrence?->status
            ? self::badge($occurrence->status->display_name, self::statusColorForOccurrence($occurrence))
            : self::badge('უცნობი', 'secondary'),
         'occ_document' => $occurrence?->document_path
            ? self::documentLink('/tasks/' . $occurrence->document_path)
            : '---',
         'recurrence_interval' => $task->recurrence_interval ? $task->recurrence_interval . " დღე" : '---',
         'start_date' => optional($occurrence?->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($occurrence?->end_date)->format('Y-m-d H:i') ?? '---',
         'created_at' => optional($task->created_at)->format('Y-m-d H:i') ?? '---',
      ];
   }

   /**
    * Build a task row for company leader task tables.
    */
   public static function companyLeaderTaskRow(Task $task): array
   {
      $columns = self::buildTaskColumns($task);

      return self::pickColumns($columns, [
         'id',
         'status',
         'worker',
         'branch',
         'service',
         'document',
         'due_date',
         'start_date',
         'end_date',
      ]);
   }

   /**
    * Build a task row for responsible person task tables.
    */
   public static function responsiblePersonTaskRow(Task $task): array
   {
      $columns = self::buildTaskColumns($task);

      return self::pickColumns($columns, [
         'id',
         'status',
         'payment_status',
         'worker',
         'branch',
         'service',
         'document',
         'due_date',
         'start_date',
         'end_date',
      ]);
   }

   /**
    * Build a payment row for responsible person dashboards.
    */
   public static function responsiblePersonPaymentRow(TaskOccurrence $occurrence): array
   {
      return [
         'id' => $occurrence->id,
         'branch' => $occurrence->branch_name_snapshot ?? '---',
         'service' => $occurrence->service_name_snapshot ?? '---',
         'due_date' => optional($occurrence->due_date)->format('Y-m-d') ?? '---',
         'payment_status' => self::paymentStatusBadge($occurrence->payment_status),
      ];
   }

   /**
    * Build a task row for worker dashboards.
    */
   public static function workerTaskRow(Task $task): array
   {
      $columns = self::buildTaskColumns(
         $task,
         after: function (array $columns, Task $task): array {
            $columns['coworker'] = self::formatCoworkers($task);
            return $columns;
         }
      );

      return self::pickColumns($columns, [
         'id',
         'status',
         'branch',
         'service',
         'coworker',
         'document',
         'due_date',
         'start_date',
         'end_date',
      ]);
   }

   /**
    * Build a branch row for branch tables.
    */
   public static function branchRow(Branch $branch): array
   {
      return [
         'id' => $branch->id,
         'name' => $branch->name ?? 'უცნობი',
         'address' => $branch->address ?? 'უცნობი',
         'company' => $branch->company->name ?? 'არ ჰყავს',
      ];
   }

   /**
    * Build an SMS log row for admin table.
    */
   public static function smsLogRow(SmsLog $smsLog, $actionResolver = null, ): array
   {
      $actions = $actionResolver ? $actionResolver($smsLog) : null;
      return [
         'id' => $smsLog->id,
         'provider' => e($smsLog->provider ?? '---'),
         'status' => self::smsStatusBadge($smsLog->status),
         'destination' => e($smsLog->destination ?? '---'),
         'smsno' => self::smsnoLabel($smsLog->smsno),
         'content' => e($smsLog->content ?? '---'),
         'provider_message_id' => e($smsLog->provider_message_id ?? '---'),
         'sent_at' => optional($smsLog->sent_at)->format('Y-m-d H:i') ?? '---',
         'created_at' => optional($smsLog->created_at)->format('Y-m-d H:i') ?? '---',
         'actions' => $actions ?? '',
      ];
   }

   /**
    * Build task occurrence rows for the admin occurrences modal.
    *
    * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $occurrences
    * @param int|null $latestId
    * @param callable|null $actionResolver
    * @param Task|null $task
    * @return \Illuminate\Support\Collection
    */
   public static function formatOccurrences($occurrences, ?int $latestId = null, ?callable $actionResolver = null, ?Task $task = null)
   {
      return $occurrences->map(function ($occurrence) use ($latestId, $actionResolver, $task) {
         $isLatest = $latestId && $occurrence->id === $latestId;
         $actions = $actionResolver ? $actionResolver($occurrence) : null;
         $statusColor = self::statusColorForOccurrence($occurrence);

         $branchModel = $task?->branch;
         $serviceModel = $task?->service;

         return [
            'id' => ($isLatest ? " <span class='badge bg-success ms-2'>$occurrence->id</span>" : $occurrence->id),
            'visibility' => self::badge(
               $occurrence->visibility ? 'ხილული' : 'დამალული',
               $occurrence->visibility ? 'success' : 'danger'
            ),
            'branch' => self::snapshotLink(
               $branchModel,
               'branches.index',
               $occurrence->branch_name_snapshot,
               $branchModel?->name
            ),
            'service' => self::snapshotLink(
               $serviceModel,
               'services.index',
               $occurrence->service_name_snapshot,
               $serviceModel?->title->ka ?? $serviceModel?->title->en
            ),
            'workers' => self::formatOccurrenceWorkers($occurrence),
            'status' => '<span class="badge bg-' . e($statusColor) . '">' . e($occurrence->status?->display_name ?? 'უცნობი') . '</span>',
            'payment_status' => self::paymentStatusBadge($occurrence->payment_status),
            'due_date' => optional($occurrence->due_date)->format('Y-m-d') ?? '---',
            'start_date' => optional($occurrence->start_date)->format('Y-m-d H:i') ?? '---',
            'end_date' => optional($occurrence->end_date)->format('Y-m-d H:i') ?? '---',
            'document' => $occurrence->document_path
               ? self::documentLink('/tasks/' . $occurrence->document_path)
               : '---',
            'actions' => $actions ?? '',
         ];
      });
   }

   /**
    * Build a shared task column map with optional hooks.
    */
   private static function buildTaskColumns(
      Task $task,
      ?callable $before = null,
      ?callable $after = null
   ): array {
      $baseColumns = [
         'id' => $task->id,
         'status' => self::badge(
            $task->latestOccurrence?->status?->display_name ?? 'უცნობი',
            self::statusColorForOccurrence($task->latestOccurrence)
         ),
         'worker' => self::formatTaskWorkers($task),
         'branch' => $task->branch_name_snapshot ?? $task->latestOccurrence?->branch_name_snapshot ?? 'უცნობი',
         'service' => $task->service?->title->ka
            ?? $task->service?->title->en
            ?? $task->service_name_snapshot
            ?? $task->latestOccurrence?->service_name_snapshot
            ?? 'უცნობი',
         'document' => $task->latestOccurrence?->document_path
            ? self::documentLink('/tasks/' . $task->latestOccurrence->document_path)
            : '---',
         'payment_status' => $task->latestOccurrence?->payment_status
            ? self::paymentStatusBadge($task->latestOccurrence->payment_status)
            : 'უცნობი',
         'due_date' => optional($task->latestOccurrence?->due_date)->format('Y-m-d') ?? 'არ მეორდება',
         'start_date' => optional($task->latestOccurrence?->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($task->latestOccurrence?->end_date)->format('Y-m-d H:i') ?? '---',
      ];

      $columns = [];

      if ($before) {
         $columns = $before($columns, $task) ?? $columns;
      }

      $columns = array_merge($columns, $baseColumns);

      if ($after) {
         $columns = $after($columns, $task) ?? $columns;
      }

      return $columns;
   }


   /**
    * Format assigned task workers into badges or a dropdown.
    */
   private static function formatTaskWorkers(Task $task): string
   {
      return self::formatWorkersCollection(
         $task->users,
         fn($user) => $user->full_name
      );
   }

   /**
    * Format coworkers by excluding the authenticated user.
    */
   private static function formatCoworkers(Task $task): string
   {
      $occurrenceWorkers = $task->latestOccurrence?->workers ?? collect();

      $filteredWorkers = $occurrenceWorkers->where('worker_id_snapshot', '!=', auth()->id());

      return self::formatWorkersCollection(
         $filteredWorkers,
         fn($worker) => $worker->worker_name_snapshot ?? 'უცნობი'
      );
   }

   /**
    * Format occurrence workers using snapshot data.
    */
   private static function formatOccurrenceWorkers($occurrence): string
   {
      return self::formatWorkersCollection(
         $occurrence->workers ?? collect(),
         fn($worker) => $worker->worker_name_snapshot ?? 'უცნობი',
      );
   }

   /**
    * Format a worker list into a single badge or a dropdown.
    */
   private static function formatWorkersCollection($workers, callable $nameResolver): string
   {
      $names = collect($workers)
         ->map(fn($worker) => trim((string) $nameResolver($worker)))
         ->filter();

      $count = $names->count();

      if ($count === 0) {
         return self::badge('არ ჰყავს', 'danger');
      }

      if ($count === 1) {
         return self::badge(e($names->first()), 'secondary');
      }

      $options = $names
         ->map(fn($name) => '<option disabled>' . e($name) . '</option>')
         ->implode('');

      return '<select class="form-select form-select-sm"><option selected>სია...</option>' .
         $options .
         '</select>';
   }

   // =====================
   // Helpers
   // =====================

   /**
    * Select a subset of columns while preserving key order.
    */
   private static function pickColumns(array $columns, array $keys): array
   {
      $picked = [];
      foreach ($keys as $key) {
         if (array_key_exists($key, $columns)) {
            $picked[$key] = $columns[$key];
         }
      }

      return $picked;
   }

   /**
    * Render a branch/service snapshot link with an outdated badge.
    */
   private static function snapshotLink(?Model $model, string $route, ?string $snapshotName, ?string $currentName): string
   {
      $name = $snapshotName ?? $currentName ?? '---';
      $isOutdated = $snapshotName && $currentName && $snapshotName !== $currentName;
      $outdatedBadge = $isOutdated
         ? "<span class='badge bg-warning text-dark me-1' title='ახლა: " . e($currentName) . "'><i class='bi bi-info-circle-fill'></i></span>"
         : '';

      if ($model) {
         return $outdatedBadge . '<a href="' . route($route, $model->id) . '" class="text-decoration-underline text-dark">' . e($name) . '</a>';
      }

      $fallback = $snapshotName ?? '---';
      return $outdatedBadge . '<span class="text-decoration-line-through">' . e($fallback) . '</span>';
   }

   /**
    * Render a Bootstrap badge for status or helper labels.
    */
   private static function badge(string $text, string $color): string
   {
      return '<span class="badge bg-' . e($color) . '">' . e($text) . '</span>';
   }

   /**
    * Render a route link if a model exists, otherwise show a strikethrough label.
    */
   private static function link(?Model $model, string $route, string $label, string $fallback): string
   {
      return $model
         ? '<a href="' . route($route, $model->id) . '" class="text-decoration-underline text-dark">' . e($label) . '</a>'
         : '<span class="text-decoration-line-through">' . e($fallback) . '</span>';
   }

   /**
    * Map occurrence status to a Bootstrap color class.
    */
   private static function statusColorForOccurrence($occurrence): string
   {
      return [
         'pending' => 'warning',
         'in_progress' => 'info',
         'completed' => 'success',
         'on_hold' => 'secondary',
         'cancelled' => 'danger',
      ][$occurrence?->status?->name] ?? 'secondary';
   }

   /**
    * Render a badge for SMS status.
    */
   private static function smsStatusBadge(?int $status): string
   {
      $map = [
         0 => ['label' => 'მოლოდინში', 'class' => 'warning'],
         1 => ['label' => 'გაგზავნილი', 'class' => 'success'],
         2 => ['label' => 'ვერ გაიგზავნა', 'class' => 'danger'],
      ];

      $entry = $map[$status ?? -1] ?? ['label' => $status !== null ? (string) $status : 'უცნობი', 'class' => 'secondary'];
      return '<span class="badge bg-' . e($entry['class']) . '">' . e($entry['label']) . '</span>';
   }

   /**
    * Translate smsno to a readable label.
    */
   private static function smsnoLabel(?int $smsno): string
   {
      return [
         1 => 'რეკლამა',
         2 => 'ინფორმაცია',
      ][$smsno ?? -1] ?? ($smsno !== null ? (string) $smsno : '---');
   }

   /**
    * Render a badge for payment status.
    */
   private static function paymentStatusBadge(string $status): string
   {
      $map = [
         'paid' => ['label' => 'გადახდილი', 'class' => 'success'],
         'unpaid' => ['label' => 'გადასახდელი', 'class' => 'danger'],
         'pending' => ['label' => 'მოლოდინში', 'class' => 'warning'],
         'overdue' => ['label' => 'ვადაგადაცილებული', 'class' => 'danger'],
      ];

      $entry = $map[$status] ?? ['label' => $status, 'class' => 'secondary'];
      return '<span class="badge bg-' . e($entry['class']) . '">' . e($entry['label']) . '</span>';
   }

   /**
    * Render a document link for PDFs or downloadable files.
    */
   private static function documentLink(string $path): string
   {
      $url = asset('documents/' . ltrim($path, '/'));
      $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      $label = strtoupper($extension) . ' ფაილი';

      if ($extension === 'pdf') {
         return '<a data-fancybox data-type="pdf" data-src="' . e($url) . '" href="javascript:;" 
                    class="text-primary text-decoration-underline">'
            . '<i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i>'
            . e($label)
            . '</a>';
      }

      return '<a href="' . e($url) . '" download 
                class="text-primary text-decoration-underline">'
         . '<i class="bi bi-file-earmark-arrow-down me-1 text-success"></i>'
         . e($label)
         . '</a>';
   }
}

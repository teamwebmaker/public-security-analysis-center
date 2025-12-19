<?php

namespace App\Presenters;

use App\Models\Branch;
use App\Models\Task;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;


class TableRowDataPresenter
{
   /**
    * Format the given model data based on context.
    *
    * @param Model $model
    * @param string $context
    * @return array
    */
   public static function format(Model $model, string $context = 'default'): array
   {
      if ($context === 'admin') {
         return self::formatAdmin($model);
      }

      if ($context === 'management') {
         return self::formatManagement($model);
      }

      if ($context === 'management_worker') {
         return self::formatManagementWorker($model);
      }

      if ($context === 'branches') {
         return self::formatBranches($model);
      }

      return []; // default
   }
   /**
    * Format task data for admin context.
    *
    * @param Model $model
    * @return array
    *
    * @throws InvalidArgumentException
    */
   private static function formatAdmin(Model $model): array
   {
      if (!$model instanceof Task) {
         throw new InvalidArgumentException('Expected instance of Task');
      }

      return [
         'id' => $model->id,
         'visibility' => self::badge(
            $model->visibility ? 'ხილული' : 'დამალული',
            $model->visibility ? 'success' : 'danger'
         ),
         'worker' => self::formatWorker($model),
         'branch' => self::link(
            $model->branch,
            'branches.index',
            $model->branch?->name ?? $model->branch_name_snapshot ?? '---',
            $model->branch_name_snapshot ?? '---'
         ),
         'service' => self::link(
            $model->service,
            'services.index',
            $model->service?->title->ka
            ?? $model->service?->title->en
            ?? $model->service_name_snapshot
            ?? 'უცნობი',
            $model->service_name_snapshot ?? 'უცნობი'
         ),
         'occ_status' => $model->latestOccurrence?->status
            ? self::badge($model->latestOccurrence->status->display_name, self::statusColorForOccurrence($model->latestOccurrence))
            : self::badge('უცნობი', 'secondary'),
         'occ_document' => $model->latestOccurrence?->document_path
            ? self::documentLink('/tasks/' . $model->latestOccurrence->document_path)
            : '---',
         'recurrence_interval' => $model->recurrence_interval ? $model->recurrence_interval . " დღე" : '---',
         // 'is_recurring' => $model->is_recurring ? self::badge('დიახ', 'info') : self::badge('არა', 'secondary'),
         'start_date' => optional($model->latestOccurrence?->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($model->latestOccurrence?->end_date)->format('Y-m-d H:i') ?? '---',
         'created_at' => optional($model->created_at)->format('Y-m-d H:i') ?? '---',
      ];
   }

   /**
    * Format task occurrence rows for table component.
    *
    * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $occurrences
    * @param int|null $latestId
    * @param callable|null $actionResolver optional resolver to generate actions per occurrence
    * @param Task|null $task parent task to read branch/service/workers without extra queries
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
            'visibility' => self::badge(
               $occurrence->visibility ? 'ხილული' : 'დამალული',
               $occurrence->visibility ? 'success' : 'danger'
            ),
            'status' => '<span class="badge bg-' . e($statusColor) . '">' . e($occurrence->status?->display_name ?? 'უცნობი') . '</span>',
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
    * Format task data for management users context.
    *
    * @param Model $model
    * @return array
    *
    * @throws InvalidArgumentException
    */
   private static function formatManagement(Model $model): array
   {
      if (!$model instanceof Task) {
         throw new InvalidArgumentException('Expected instance of Task');
      }

      return [
         'id' => $model->id,
         'status' => self::badge(
            $model->latestOccurrence?->status?->display_name ?? 'უცნობი',
            self::statusColorForOccurrence($model->latestOccurrence)
         ),
         'worker' => self::formatWorker($model),
         'branch' => $model->branch_name_snapshot ?? $model->latestOccurrence?->branch_name_snapshot ?? 'უცნობი',
         'service' => $model->service?->title->ka
            ?? $model->service?->title->en
            ?? $model->service_name_snapshot
            ?? $model->latestOccurrence?->service_name_snapshot
            ?? 'უცნობი',
         'document' => $model->latestOccurrence?->document_path
            ? self::documentLink('/tasks/' . $model->latestOccurrence->document_path)
            : '---',
         'due_date' => optional($model->latestOccurrence?->due_date)->format('Y-m-d') ?? 'არ მეორდება',
         'start_date' => optional($model->latestOccurrence?->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($model->latestOccurrence?->end_date)->format('Y-m-d H:i') ?? '---',

      ];
   }

   /**
    * Format task data for worker tasks context.
    *
    * @param Model $model
    * @return array
    *
    * @throws InvalidArgumentException
    */
   private static function formatManagementWorker(Model $model): array
   {
      if (!$model instanceof Task) {
         throw new InvalidArgumentException('Expected instance of Task');
      }

      return [
         'id' => $model->id,
         'status' => self::badge(
            $model->latestOccurrence?->status?->display_name ?? 'უცნობი',
            self::statusColorForOccurrence($model->latestOccurrence)
         ),
         'branch' => $model->branch_name_snapshot ?? $model->latestOccurrence?->branch_name_snapshot ?? 'უცნობი',
         'service' => $model->service?->title->ka
            ?? $model->service?->title->en
            ?? $model->service_name_snapshot
            ?? $model->latestOccurrence?->service_name_snapshot
            ?? 'უცნობი',
         'Coworker' => self::formatWorker(
            $model->setRelation(
               'users',
               $model->users->where('id', '!=', auth()->id())
            )
         ),
         'document' => $model->latestOccurrence?->document_path
            ? self::documentLink('/tasks/' . $model->latestOccurrence->document_path)
            : '---',
         'due_date' => optional($model->latestOccurrence?->due_date)->format('Y-m-d') ?? 'არ მეორდება',
         'start_date' => optional($model->latestOccurrence?->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($model->latestOccurrence?->end_date)->format('Y-m-d H:i') ?? '---',
      ];
   }

   /**
    * Helper: Format worker display depending on the number of assigned users.
    *
    * @param Task $task
    * @return string
    */
   private static function formatWorker(Task $task): string
   {
      return self::formatWorkersCollection(
         $task->users,
         fn($user) => $user->full_name
      );
   }

   /**
    * Format branch data.

    * @param Model $model
    * @return array
    *
    * @throws InvalidArgumentException
    */
   private static function formatBranches(Model $model): array
   {
      if (!$model instanceof Branch) {
         throw new InvalidArgumentException('Expected instance of Branch');
      }

      return [
         'id' => $model->id,
         'name' => $model->name ?? 'უცნობი',
         'address' => $model->address ?? 'უცნობი',
         'company' => $model->company->name ?? 'არ ჰყავს',
      ];
   }

   /**
    * Helper: Render a Bootstrap badge HTML element.
    *
    * @param string $text
    * @param string $color
    * @return string
    */
   private static function badge(string $text, string $color): string
   {
      return '<span class="badge bg-' . e($color) . '">' . e($text) . '</span>';
   }

   /**
    * Helper: Render a hyperlink if model exists, otherwise fallback with strikethrough.
    *
    * @param Model|null $model
    * @param string $route
    * @param string $label
    * @param string $fallback
    * @return string
    */
   private static function link(?Model $model, string $route, string $label, string $fallback): string
   {
      return $model
         ? '<a href="' . route($route, $model->id) . '" class="text-decoration-underline text-dark">' . e($label) . '</a>'
         : '<span class="text-decoration-line-through">' . e($fallback) . '</span>';
   }

   /**
    * Helper: Determine Bootstrap color class based on task status.
    *
    * @param Task $task
    * @return string
    */
   private static function statusColor(Task $task): string
   {
      return [
         'pending' => 'warning',
         'in_progress' => 'info',
         'completed' => 'success',
         'on_hold' => 'secondary',
         'cancelled' => 'danger',
      ][$task->status?->name] ?? 'secondary';
   }

   /**
    * Helper: Render snapshot label with link when model still exists. Warn if snapshot outdated.
    */
   private static function snapshotLink(?Model $model, string $route, ?string $snapshotName, ?string $currentName): string
   {
      // Always display the snapshot name when available; fallback to current name.
      $name = $snapshotName ?? $currentName ?? '---';

      // Only warn if both values exist and differ. Prefer current name in tooltip for clarity.
      $isOutdated = $snapshotName && $currentName && $snapshotName !== $currentName;
      $outdatedBadge = $isOutdated
         ? "<span class='badge bg-warning text-dark me-1' title='ახლა: " . e($currentName) . "'><i class='bi bi-info-circle-fill'></i></span>"
         : '';

      if ($model) {
         return $outdatedBadge . '<a href="' . route($route, $model->id) . '" class="text-decoration-underline text-dark">' . e($name) . '</a>';
      }

      // If we have a snapshot name but no model, use snapshot; otherwise show placeholder.
      $fallback = $snapshotName ?? '---';
      return $outdatedBadge . '<span class="text-decoration-line-through">' . e($fallback) . '</span>';
   }

   /**
    * Helper: Format occurrence workers from snapshot records.
    */
   private static function formatOccurrenceWorkers($occurrence): string
   {
      return self::formatWorkersCollection(
         $occurrence->workers ?? collect(),
         fn($worker) => $worker->worker_name_snapshot ?? 'უცნობი',
      );
   }

   /**
    * Helper: Shared worker formatter for tasks and occurrences.
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

   /**
    * Helper: Determine Bootstrap color class based on occurrence status.
    */
   private static function statusColorForOccurrence($occurrence): string
   {
      return [
         'pending' => 'warning',
         'in_progress' => 'info',
         'completed' => 'success',
         'on_hold' => 'secondary',
         'cancelled' => 'danger',
      ][$occurrence->status?->name] ?? 'secondary';
   }


   /**
    * Helper: Render a document link using Fancybox for PDFs, download for others.
    *
    * @param string $path
    * @return string
    */
   private static function documentLink(string $path): string
   {
      $url = asset('documents/' . ltrim($path, '/'));
      $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      $label = strtoupper($extension) . ' ფაილი';

      // PDFs open in Fancybox
      if ($extension === 'pdf') {
         return '<a data-fancybox data-type="pdf" data-src="' . e($url) . '" href="javascript:;" 
                    class="text-primary text-decoration-underline">'
            . '<i class="bi bi-file-earmark-pdf-fill me-1 text-danger"></i>'
            . e($label)
            . '</a>';
      }

      // Other document types trigger download
      return '<a href="' . e($url) . '" download 
                class="text-primary text-decoration-underline">'
         . '<i class="bi bi-file-earmark-arrow-down me-1 text-success"></i>'
         . e($label)
         . '</a>';
   }
}

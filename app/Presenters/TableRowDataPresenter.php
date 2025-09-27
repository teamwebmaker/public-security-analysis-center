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
         'status' => self::badge($model->status?->display_name ?? 'უცნობი', self::statusColor($model)),
         'worker' => self::formatWorker($model),
         'branch' => self::link($model->branch, 'branches.index', $model->branch?->name, $model->branch_name),
         'service' => self::link(
            $model->service,
            'services.index',
            $model->service?->title->ka ?? $model->service?->title->en ?? 'უცნობი',
            $model->service_name
         ),
         'visibility' => self::badge(
            $model->visibility ? 'ხილული' : 'დამალული',
            $model->visibility ? 'success' : 'danger'
         ),
         'start_date' => optional($model->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($model->end_date)->format('Y-m-d H:i') ?? '---',
         'created_at' => optional($model->created_at)->format('Y-m-d H:i') ?? '---',
      ];
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
         'status' => self::badge($model->status?->display_name ?? 'უცნობი', self::statusColor($model)),
         'worker' => self::formatWorker($model),
         'branch' => $model->branch_name ?? 'უცნობი',
         'service' => $model->service?->title->ka ?? $model->service?->title->en ?? 'უცნობი',

         'start_date' => optional($model->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($model->end_date)->format('Y-m-d H:i') ?? '---',

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
         'status' => self::badge($model->status?->display_name ?? 'უცნობი', self::statusColor($model)),
         'branch' => $model->branch_name ?? 'უცნობი',
         'service' => $model->service?->title->ka ?? $model->service?->title->en ?? 'უცნობი',
         'start_date' => optional($model->start_date)->format('Y-m-d H:i') ?? '---',
         'end_date' => optional($model->end_date)->format('Y-m-d H:i') ?? '---',
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
      $count = $task->users->count();

      if ($count === 1) {
         return self::badge(e($task->users->first()->full_name), 'secondary');
      }

      if ($count >= 2) {
         return '<select class="form-select form-select-sm"><option selected>სია...</option>' .
            $task->users->map(fn($u) => '<option disabled>' . e($u->full_name) . '</option>')->implode('') .
            '</select>';
      }

      return self::badge('არ ჰყავს', 'danger');
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
         'created_at' => optional($model->created_at)->format('Y-m-d H:i') ?? '---',
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

}

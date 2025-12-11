<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskOccurrenceStatus;
use App\Models\User;
use App\Presenters\TableHeaderDataPresenter;
use App\Presenters\TableRowDataPresenter;
use App\QueryBuilders\Sorts\LatestOccurrenceEndDateSort;
use App\QueryBuilders\Sorts\LatestOccurrenceStartDateSort;
use App\Services\Tasks\TaskCreator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class TaskController extends CrudController
{
   use SyncsRelations;

   protected string $modelClass = Task::class;
   protected string $contextField = "task";
   protected string $contextFieldPlural = "tasks";
   protected string $resourceName = "tasks";
   protected array $modelRelations = ["users", "branch", "service"];

   protected int $perPage = 10;

   protected array $fileFields = [];

   protected TaskCreator $taskCreator;

   public function __construct(TaskCreator $taskCreator)
   {
      $this->taskCreator = $taskCreator;
   }

   /**
    * Display a listing of the resource.
    * And accept a searching and filtering
    */

   public function index(Request $request)
   {
      $tasks = QueryBuilder::for(Task::class)
         // Eager-loadable relations
         ->allowedIncludes([
            'users',
            'branch',
            'service',
            'latestOccurrence',
            'latestOccurrence.status',
         ])
         // Sorting options including custom sorts for latest occurrence dates
         ->allowedSorts([
            'created_at',
            'recurrence_interval',
            AllowedSort::custom('latest_start_date', new LatestOccurrenceStartDateSort()),
            AllowedSort::custom('latest_end_date', new LatestOccurrenceEndDateSort()),
         ])
         // Default sort (DESC)
         ->defaultSort('-created_at')
         ->allowedFilters([
            AllowedFilter::callback('search', function ($query, $value) {
               $value = is_array($value) ? $value[0] : $value;
               $like = '%' . $value . '%';

               $query->where(function ($q) use ($like) {
                  $q->where('branch_name_snapshot', 'LIKE', $like)
                     ->orWhere('service_name_snapshot', 'LIKE', $like)
                     ->orWhereHas('branch', function ($q) use ($like) {
                        $q->where('name', 'LIKE', $like);
                     })->orWhereHas('service', function ($q) use ($like) {
                        $q->where('title->ka', 'LIKE', $like);
                     })->orWhereHas('users', function ($q) use ($like) {
                        $q->where('full_name', 'LIKE', $like);
                     });
               });
            }),

            AllowedFilter::exact('branch_id'),
            AllowedFilter::exact('service_id'),
            AllowedFilter::exact('visibility'),
            AllowedFilter::exact('is_recurring'),
            AllowedFilter::exact('recurrence_interval'),

            AllowedFilter::callback('occurrence_status', function ($query, $value) {
               $query->whereHas('latestOccurrence.status', function ($q) use ($value) {
                  $q->where('name', $value)
                     ->orWhere('display_name', $value);
               });
            }),
         ])
         ->with([
            'users',
            'branch',
            'service',
            'latestOccurrence.status',
            'taskOccurrences.status',
            'taskOccurrences.workers',
         ])
         ->paginate($this->perPage)
         ->appends($request->query());

      $taskHeaders = TableHeaderDataPresenter::taskHeaders();
      $occurrenceHeaders = TableHeaderDataPresenter::occurrenceHeaders();

      $taskRows = $tasks->map(
         fn($task) => TableRowDataPresenter::format($task, 'admin')
      );

      $occurrenceRows = $tasks->mapWithKeys(function ($task) {
         $occurrences = $task->taskOccurrences
            ->sortByDesc('created_at');

         $latestId = $task->latestOccurrence?->id;

         $rows = TableRowDataPresenter::formatOccurrences(
            $occurrences,
            $latestId,
            function ($occurrence) {
               $editUrl = route('task-occurrences.edit', $occurrence);
               $deleteUrl = route('task-occurrences.destroy', $occurrence);
               $isLatest = $occurrence->isLatest();

               $deleteButton = '<form method="POST" action="' . e($deleteUrl) . '" onsubmit="return confirm(\'წავშალოთ ეს ციკლი?\')" class="d-inline">'
                  . csrf_field()
                  . method_field('DELETE')
                  . '<button type="submit" class="btn btn-sm btn-outline-danger' . ($isLatest ? ' disabled' : '') . '"'
                  . ($isLatest ? ' title="ბოლო ციკლი ვერ წაიშლება"' : '')
                  . '>'
                  . '<i class="bi bi-trash"></i>'
                  . '</button>'
                  . '</form>';

               return '<div class="d-flex gap-2 justify-content-end">'
                  . '<a href="' . e($editUrl) . '" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square"></i> ჩასწორება</a>'
                  . $deleteButton
                  . '</div>';
            }
         );

         return [$task->id => $rows];
      });

      // =========================
      // Filtering dropdown data
      // =========================

      // For "branch" filter select
      $branches = Branch::pluck('name', 'id')->toArray();

      // For "service" filter select (show ka first, fallback to en)
      $services = Service::all()
         ->mapWithKeys(fn($service) => [
            $service->id => $service->title->ka ?? $service->title->en,
         ])
         ->toArray();

      // For "occurrence_status" filter (name => display_name)
      $occurrenceStatuses = TaskOccurrenceStatus::orderBy('id')
         ->pluck('display_name', 'name')
         ->toArray();

      // For "recurrence_interval" filter (1–31 days)
      $intervalOptions = collect(range(1, 31))
         ->mapWithKeys(fn($day) => [$day => $day . ' დღე'])
         ->toArray();

      // =========================
      // Sortable columns mapping

      // Map human-readable column label -> backend sort key
      $sortableMap = [
         'სამუშაოს დაწყება' => 'latest_start_date',
         'სამუშაოს დასრულება' => 'latest_end_date',
         'სამუშაოს განსაზღვრა' => 'created_at',
         'განმეორების ინტერვალი' => 'recurrence_interval',
      ];

      // ===========================
      // Filters config for the UI

      $filters = [
         'visibility' => [
            'label' => 'ხილვადობა',
            'options' => ['1' => 'ხილული', '0' => 'დამალული'],
         ],
         'occurrence_status' => [
            'label' => 'სტატუსი',
            'options' => $occurrenceStatuses,
         ],
         'branch_id' => [
            'label' => 'ფილიალი',
            'options' => $branches,
         ],
         'service_id' => [
            'label' => 'სერვისი',
            'options' => $services,
         ],
         'recurrence_interval' => [
            'label' => 'განმეორების ინტერვალი',
            'options' => $intervalOptions,
         ],
         'is_recurring' => [
            'label' => 'განმეორებადი',
            'options' => ['1' => 'დიახ', '0' => 'არა'],
         ],
      ];

      // =================
      // UI helpers

      // Button config for opening occurrences modal per task
      $occurrenceModalTriggers = fn($task) => [
         [
            'modal_id' => 'task-occurrences-' . $task->id,
            'label' => 'ისტორია',
            'icon' => 'bi-clock-history',
         ],
      ];

      // =========================
      // Return view

      return view("admin.{$this->resourceName}.index", [

         $this->contextFieldPlural => $tasks,
         'resourceName' => $this->resourceName,

         // Header + rows for main Task table
         'taskRows' => $taskRows,
         'taskHeaders' => $taskHeaders,

         // Header + rows for occurrences modal table
         'occurrenceHeaders' => $occurrenceHeaders,
         'occurrenceRows' => $occurrenceRows,

         // Sortable columns & filters metadata for the UI
         'sortableMap' => $sortableMap,
         'filters' => $filters,

         // Modal trigger buttons for occurrences
         'occurrenceModalTriggers' => $occurrenceModalTriggers,
      ]);
   }

   /**
    * pass additional data to create form
    */
   protected function additionalCreateData(): array
   {
      return $this->prepareTaskFormData();
   }

   /**
    * Store a newly created resource in storage.
    */
   public function store(StoreTaskRequest $request)
   {
      $validated = $request->validated();
      $data = $this->prepareTaskData($request, $validated);

      $this->taskCreator->createWithInitialOccurrence($data);

      return redirect()
         ->route("{$this->resourceName}.index")
         ->with('success', 'სამუშაო შეიქმნა წარმატებით');
   }

   /**
    * Update the specified resource in storage.
    */

   public function update(UpdateTaskRequest $request, Task $task)
   {
      DB::transaction(function () use ($request, $task) {

         $validated = $request->validated();
         $data = $this->prepareTaskData($request, $validated, $task);

         if (!($data['is_recurring'] ?? false)) {
            $data['recurrence_interval'] = null;
         }

         $latestOccurrence = $task->latestOccurrence()->first();
         $requiresDocument = $request->has('requires_document')
            ? $request->boolean('requires_document')
            : optional($latestOccurrence)->requires_document;

         $this->syncRelations($task, $data, [
            'users' => 'user_ids',
         ]);

         $task->update($data);
         $task->load('users');

         $recalculateDueDate = $task->wasChanged('recurrence_interval') || $task->wasChanged('is_recurring');

         $updateOccurrences = ['requires_document' => $requiresDocument];

         if ($task->wasChanged('visibility')) {
            // Enum column expects "0"/"1"
            $updateOccurrences['visibility'] = $task->visibility ? '1' : '0';
         }

         // Only push changes when we have a concrete value
         if (!is_null($requiresDocument) || array_key_exists('visibility', $updateOccurrences)) {
            $task->taskOccurrences()->update($updateOccurrences);
         }

         // Sync latest occurrence snapshots to match task data
         if ($latest = $latestOccurrence) {
            $latestUpdates = [
               'branch_id_snapshot' => $task->branch_id,
               'branch_name_snapshot' => $task->branch_name_snapshot,
               'service_id_snapshot' => $task->service_id,
               'service_name_snapshot' => $task->service_name_snapshot,
            ];

            // Recalculate due date if needed
            if ($recalculateDueDate) {
               $interval = (int) ($task->recurrence_interval ?? 0);
               $latestUpdates['due_date'] = $task->is_recurring && $interval > 0
                  ? now()->addDays($interval)
                  : null;
            }

            $latest->update($latestUpdates);

            // Sync latest occurrence workers to match task workers (snapshot)
            $latest->workers()->delete();
            if ($task->users->isNotEmpty()) {
               $latest->workers()->createMany(
                  $task->users->map(fn($user) => [
                     'worker_id_snapshot' => $user->id,
                     'worker_name_snapshot' => $user->full_name,
                  ])->all()
               );
            }

         }
      });

      return redirect()
         ->back()
         ->with('success', 'სამუშაო განახლდა წარმატებით');
   }


   protected function additionalEditData(): array
   {
      return $this->prepareTaskFormData();
   }

   /**
    * Prepares data for create/edit task forms.
    */
   public function prepareTaskFormData(): array
   {
      return [
         "services" => Service::all()
            ->pluck("title.ka", "id")
            ->map(fn($ka, $id) => $ka ?: Service::find($id)->title->en)
            ->toArray(),
         "branches" => Branch::pluck("name", "id")->toArray(),
         // only workers
         "users" => User::where(
            "role_id",
            Role::where("name", "worker")->value("id")
         )
            ->select("id", "full_name")
            ->get(),
      ];
   }


   /**
    * Prepares task data for create/update by getting service and branch names and assigning them to the data array.
    */
   protected function prepareTaskData(Request $request, $data, Task $task = null): array
   {

      // Handle document upload
      $files = collect($this->fileFields)
         ->mapWithKeys(function ($path, $field) use ($request, $task) {
            $existing = $task?->$field;
            $file = $this->handleFileUpload(
               $request,
               $field,
               $path,
               $existing
            );
            return $file ? [$field => $file] : [];
         })
         ->toArray();

      // Handle branch name snapshot saving
      if (
         !empty($data["branch_id"]) &&
         (!$task || $data["branch_id"] !== $task->branch_id)
      ) {
         $branch = Branch::find($data["branch_id"]);
         if ($branch) {
            $data["branch_name_snapshot"] = $branch->name;
         }
      }
      // Handle service name snapshot saving
      if (
         !empty($data["service_id"]) &&
         (!$task || $data["service_id"] !== $task->service_id)
      ) {
         $service = Service::find($data["service_id"]);
         if ($service) {
            $data["service_name_snapshot"] =
               $service->title->ka ?? $service->title->en;
         }
      }

      return [
         ...$data,
         ...$files,
      ];
   }


   /**
    * @route PUT management/tasks/{task}
    * Route is under management prefix.
    * Used from worker dashboard to set task as in progress.
    */
   // public function editStatus(Task $task)
   // {
   //    try {
   //       // Retrieve required statuses
   //       $pendingStatusId = TaskStatus::where("name", "pending")->value("id");
   //       $inProgressStatusId = TaskStatus::where("name", "in_progress")->value("id");

   //       if (!$pendingStatusId || !$inProgressStatusId) {
   //          return redirect()
   //             ->back()
   //             ->with('error', 'მოხდა გაუთვალისწინებელი შეცდომა, გთხოვთ დაუკავშირდით დახმარებას.');
   //       }

   //       // Move only from Pending → In Progress
   //       if ($task->status_id === $pendingStatusId) {
   //          $task->update([
   //             'status_id' => $inProgressStatusId,
   //             'start_date' => now(),
   //          ]);

   //          return redirect()
   //             ->back()
   //             ->with('success', 'სამუშაო დაწყებულად მოინიშნა წარმატებით.');
   //       }

   //       return redirect()
   //          ->back()
   //          ->with('error', 'სამუშაოს სტატუსის შეცვლა ამ ეტაპზე შეუძლებელია.');

   //    } catch (\Exception $e) {
   //       Log::error('Task status update failed from worker dashboard', [
   //          'task_id' => $task->id,
   //          'error' => $e->getMessage(),
   //       ]);

   //       return redirect()
   //          ->back()
   //          ->with('error', 'სამუშაოს სტატუსის განახლება ვერ მოხერხდა. გთხოვთ, სცადეთ თავიდან.');
   //    }
   // }


   /**
    * @route PUT management/tasks/{task}/document
    * Route is under management prefix.
    * Used by workers to upload a task document and set status as completed.
    */
   // public function uploadDocument(Request $request, Task $task)
   // {
   //    try {
   //       // Validate the uploaded file
   //       $validated = $request->validate([
   //          'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
   //       ], [
   //          'document.required' => 'გთხოვთ ატვირთოთ სამუშაოს დოკუმენტი.',
   //          'document.mimes' => 'დოკუმენტის ფორმატი არასწორია. ნებადართულია: PDF, Word, Excel.',
   //          'document.max' => 'ფაილის ზომა არ უნდა აღემატებოდეს 5MB-ს.',
   //       ]);

   //       // Upload or replace the file
   //       $uploadedFile = $this->handleFileUpload(
   //          $request,
   //          'document',
   //          $this->fileFields['document'],
   //          $task->document
   //       );

   //       if (!$uploadedFile) {
   //          return redirect()
   //             ->back()
   //             ->with('error', 'დოკუმენტის ატვირთვა ვერ მოხერხდა, სცადეთ თავიდან.');
   //       }

   //       // Retrieve status IDs
   //       $inProgressStatusId = TaskStatus::where("name", "in_progress")->value("id");
   //       $completedStatusId = TaskStatus::where("name", "completed")->value("id");

   //       // Update document and mark as completed if applicable
   //       $updateData = ['document' => $uploadedFile];

   //       if ($task->status_id === $inProgressStatusId && $completedStatusId) {
   //          $updateData['status_id'] = $completedStatusId;
   //          $updateData['end_date'] = now();
   //       }

   //       $task->update($updateData);

   //       return redirect()
   //          ->back()
   //          ->with('success', 'დოკუმენტი წარმატებით აიტვირთა და სამუშაო დასრულებულად მოინიშნა.');

   //    } catch (\Exception $e) {
   //       Log::error('Task document upload failed', [
   //          'task_id' => $task->id,
   //          'error' => $e->getMessage(),
   //       ]);

   //       return redirect()
   //          ->back()
   //          ->with('error', 'დოკუმენტის ატვირთვისას მოხდა შეცდომა, გთხოვთ სცადოთ თავიდან.');
   //    }
   // }


}

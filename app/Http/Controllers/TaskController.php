<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Presenters\TableRowDataPresenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class TaskController extends CrudController
{
   use SyncsRelations;
   protected string $modelClass = Task::class;
   protected string $contextField = "task";
   protected string $contextFieldPlural = "tasks";
   protected string $resourceName = "tasks";
   protected array $modelRelations = ["status", "users", "branch", "service"];

   protected int $perPage = 10;

   protected array $fileFields = ['document' => "documents/tasks/"];

   /**
    * Display a listing of the resource.
    * And accept a searching and filtering
    */
   public function index(Request $request)
   {
      $query = QueryBuilder::for(Task::class)
         ->allowedIncludes(["status", "users", "branch", "service"])
         // Allowed sorting fields
         ->allowedSorts([
            "created_at",
            "branch_name",
            "service_name",
            "start_date",
            "end_date",
            "updated_at",
         ])
         // Allowed Search fields
         ->allowedFilters([
            AllowedFilter::callback("search", function ($query, $value) {
               $query->where(function ($q) use ($value) {
                  $q->orWhereHas(
                     "branch",
                     fn($q) => $q->where("name", "LIKE", "%$value%")
                  )
                     ->orWhereHas(
                        "service",
                        fn($q) => $q->where(
                           "title->ka",
                           "LIKE",
                           "%$value%"
                        )
                     ) // or `title`, based on your schema
                     ->orWhereHas(
                        "status",
                        fn($q) => $q->where(
                           "display_name",
                           "LIKE",
                           "%$value%"
                        )
                     )
                     ->orWhereHas(
                        "users",
                        fn($q) => $q->where(
                           "full_name",
                           "LIKE",
                           "%$value%"
                        )
                     );
               });
            }),
         ])
         ->with(["status", "users", "branch", "service"])
         ->paginate($this->perPage)
         ->appends(request()->query());

      // Row formatted data
      // $rows = $query->map(fn($task) => TableHelper::formatTaskRow($task));
      $rows = $query->map(fn($task) => TableRowDataPresenter::format($task, 'admin'));

      return view("admin.{$this->resourceName}.index", [
         $this->contextFieldPlural => $query,
         "rows" => $rows,
         "resourceName" => $this->resourceName,
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
      $validatedData = $request->validated();

      $data = $this->prepareTaskData($request, $validatedData);

      $task = $this->modelClass::create($data);

      // Sync users (remove unchecked)
      $this->syncRelations($task, $data, [
         "users" => "user_ids",
      ]);

      return redirect()
         ->route("{$this->resourceName}.index")
         ->with("success", "სამუშაო შეიქმნა წარმატებით");
   }

   /**
    * Update the specified resource in storage.
    */
   public function update(UpdateTaskRequest $request, Task $task)
   {
      $validatedData = $request->validated();
      $data = $this->prepareTaskData($request, $validatedData, $task);

      // Sync users (remove unchecked)
      $this->syncRelations($task, $data, [
         "users" => "user_ids",
      ]);

      $task->update($data);

      return redirect()
         ->back()
         ->with("success", "სამუშაო განახლდა წარმატებით");
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
         "statuses" => TaskStatus::orderBy("id")
            ->pluck("display_name", "id")
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

      // Handle branch name saving
      if (
         !empty($data["branch_id"]) &&
         (!$task || $data["branch_id"] !== $task->branch_id)
      ) {
         $branch = Branch::find($data["branch_id"]);
         if ($branch) {
            $data["branch_name"] = $branch->name;
         }
      }
      // Handle branch name saving
      if (
         !empty($data["service_id"]) &&
         (!$task || $data["service_id"] !== $task->service_id)
      ) {
         $service = Service::find($data["service_id"]);
         if ($service) {
            $data["service_name"] =
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
   public function editStatus(Task $task)
   {
      try {
         // Retrieve required statuses
         $pendingStatusId = TaskStatus::where("name", "pending")->value("id");
         $inProgressStatusId = TaskStatus::where("name", "in_progress")->value("id");

         if (!$pendingStatusId || !$inProgressStatusId) {
            return redirect()
               ->back()
               ->with('error', 'მოხდა გაუთვალისწინებელი შეცდომა, გთხოვთ დაუკავშირდით დახმარებას.');
         }

         // Move only from Pending → In Progress
         if ($task->status_id === $pendingStatusId) {
            $task->update([
               'status_id' => $inProgressStatusId,
               'start_date' => now(),
            ]);

            return redirect()
               ->back()
               ->with('success', 'სამუშაო დაწყებულად მოინიშნა წარმატებით.');
         }

         return redirect()
            ->back()
            ->with('error', 'სამუშაოს სტატუსის შეცვლა ამ ეტაპზე შეუძლებელია.');

      } catch (\Exception $e) {
         Log::error('Task status update failed from worker dashboard', [
            'task_id' => $task->id,
            'error' => $e->getMessage(),
         ]);

         return redirect()
            ->back()
            ->with('error', 'სამუშაოს სტატუსის განახლება ვერ მოხერხდა. გთხოვთ, სცადეთ თავიდან.');
      }
   }


   /**
    * @route PUT management/tasks/{task}/document
    * Route is under management prefix.
    * Used by workers to upload a task document and set status as completed.
    */
   public function uploadDocument(Request $request, Task $task)
   {
      try {
         // Validate the uploaded file
         $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
         ], [
            'document.required' => 'გთხოვთ ატვირთოთ სამუშაოს დოკუმენტი.',
            'document.mimes' => 'დოკუმენტის ფორმატი არასწორია. ნებადართულია: PDF, Word, Excel.',
            'document.max' => 'ფაილის ზომა არ უნდა აღემატებოდეს 5MB-ს.',
         ]);

         // Upload or replace the file
         $uploadedFile = $this->handleFileUpload(
            $request,
            'document',
            $this->fileFields['document'],
            $task->document
         );

         if (!$uploadedFile) {
            return redirect()
               ->back()
               ->with('error', 'დოკუმენტის ატვირთვა ვერ მოხერხდა, სცადეთ თავიდან.');
         }

         // Retrieve status IDs
         $inProgressStatusId = TaskStatus::where("name", "in_progress")->value("id");
         $completedStatusId = TaskStatus::where("name", "completed")->value("id");

         // Update document and mark as completed if applicable
         $updateData = ['document' => $uploadedFile];

         if ($task->status_id === $inProgressStatusId && $completedStatusId) {
            $updateData['status_id'] = $completedStatusId;
            $updateData['end_date'] = now();
         }

         $task->update($updateData);

         return redirect()
            ->back()
            ->with('success', 'დოკუმენტი წარმატებით აიტვირთა და სამუშაო დასრულებულად მოინიშნა.');

      } catch (\Exception $e) {
         Log::error('Task document upload failed', [
            'task_id' => $task->id,
            'error' => $e->getMessage(),
         ]);

         return redirect()
            ->back()
            ->with('error', 'დოკუმენტის ატვირთვისას მოხდა შეცდომა, გთხოვთ სცადოთ თავიდან.');
      }
   }


}

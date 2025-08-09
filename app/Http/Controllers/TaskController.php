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
      $data = $this->prepareTaskData($request->validated());

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
      $data = $this->prepareTaskData($request->validated(), $task);

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
   protected function prepareTaskData(array $data, Task $task = null): array
   {
      // Get branch and service names and assign them
      if (
         !empty($data["branch_id"]) &&
         (!$task || $data["branch_id"] !== $task->branch_id)
      ) {
         $branch = Branch::find($data["branch_id"]);
         if ($branch) {
            $data["branch_name"] = $branch->name;
         }
      }

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

      return $data;
   }


   /**
    * @route PUT management/tasks/{task}
    * route is under management. prefix
    * Currently used from worker dashboard 
    * @param \App\Models\Task $task
    * @return \Illuminate\Http\RedirectResponse
    */
   public function editStatus(Task $task)
   {
      try {
         // Retrieve status IDs
         $pendingStatusId = TaskStatus::where("name", "pending")->value("id");
         $inProgressStatusId = TaskStatus::where("name", "in_progress")->value("id");
         $completedStatusId = TaskStatus::where("name", "completed")->value("id");

         // Check if all required statuses exist
         if (!$pendingStatusId || !$inProgressStatusId || !$completedStatusId) {
            return redirect()
               ->back()
               ->with('error', 'მოხდა გაუთვალისწინებელი შეცდომა, სტატუსების დაყენება ვერ მოხერხდა, გთხოვთ დაუკავშირდით დახმარებას.');
         }

         // Update status based on current state
         if ($task->status_id === $pendingStatusId) {
            $task->status_id = $inProgressStatusId;
            $task->start_date = now(); // When in progress set start date
         } elseif ($task->status_id === $inProgressStatusId) {
            $task->status_id = $completedStatusId;
            $task->end_date = now(); // When completed set end date
         } else {
            return redirect()
               ->back()
               ->with('warning', 'სამუშაო უკვე დასრულებულია ან არასწორი სტატუსია.');
         }

         $task->save();
         // Redirect back with success message
         return redirect()
            ->back()
            ->with('success', 'სამუშაოს სტატუსი განახლდა წარმატებით');

      } catch (\Exception $e) {
         // Log the error
         Log::error('Task status update failed from worker dashboard', [
            'task_id' => $task->id,
            'error' => $e->getMessage(),
         ]);

         // If try catch fails return error
         return redirect()
            ->back()
            ->with('error', 'სამუშაოს სტატუსის განახლება ვერ მოხერხდა. გთხოვთ, სცადეთ თავიდან.');
      }
   }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\TaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends CrudController
{
    use SyncsRelations;
    protected string $modelClass = Task::class;
    protected string $contextField = "task";
    protected string $contextFieldPlural = "tasks";
    protected string $resourceName = "tasks";
    protected array $modelRelations = ["status", 'users', "branch", "service"];

    protected int $perPage = 10;

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
            'users' => 'user_ids',
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
            'users' => 'user_ids',
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


    public function prepareTaskFormData(): array
    {
        return [
            "services" => Service::all()
                ->pluck("title.ka", "id")
                ->map(fn($ka, $id) => $ka ?: Service::find($id)->title->en)
                ->toArray(),
            "statuses" => TaskStatus::orderBy('id')->pluck("display_name", "id")->toArray(),
            "branches" => Branch::pluck("name", "id")->toArray(),
            // only workers
            "users" => User::where('role_id', Role::where('name', 'worker')->value('id'))->select('id', 'full_name')->get()
        ];
    }

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
}

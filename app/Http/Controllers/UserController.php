<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use App\Policies\UserConnectionPolicy;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class UserController extends CrudController
{
    use SyncsRelations;

    // Configuration for base CrudController behavior
    protected string $modelClass = User::class;
    protected string $contextField = "user";
    protected string $contextFieldPlural = "users";
    protected string $resourceName = "users";
    protected array $modelRelations = ['role', 'companies', 'branches', 'tasks', 'tasks.service'];
    protected array $localScopes = ['withoutAdmins'];

    /**
     * Returns additional data for the create form (roles, companies, etc.).
     */
    protected function additionalCreateData(): array
    {
        return $this->prepareUserFormData();
    }

    /**
     * Returns additional data for the edit form (same as create).
     */
    protected function additionalEditData(): array
    {
        return $this->prepareUserFormData();
    }

    /**
     * Stores a new user and syncs only authorized relations (based on role).
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        // Just a temporary model instance (by this we are avoiding to create the user if getAuthorizedRelations fails)
        $user = new User($data);

        // Attach only allowed relations according to user role
        $authorizedRelations = $this->getAuthorizedRelations($user, $data);
        $this->syncRelations($user, $data, $authorizedRelations);

        // If no errors occurred, create the user
        $this->modelClass::create($data);

        return redirect()
            ->route("{$this->resourceName}.index")
            ->with("success", "მომხმარებელი შეიქმნა წარმატებით");
    }

    /**
     * Updates an existing user and syncs only authorized relations (based on role).
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);

        // Attach only allowed relations according to user role
        $authorizedRelations = $this->getAuthorizedRelations($user, $data);
        $this->syncRelations($user, $data, $authorizedRelations);

        return redirect()
            ->back()
            ->with("success", "მომხმარებელი განახლდა წარმატებით");
    }

    /**
     * Returns form data for user create/edit views.
     * Task: Includes only active task statuses and formatted service/branch names.
     */
    protected function prepareUserFormData(): array
    {
        $statusIds = TaskStatus::whereIn('name', Task::workerAssignableTaskStatuses())->pluck('id');

        return [
            'roles' => Role::withoutAdmins()->get(),
            'companies' => Company::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'services' => Service::select('id', 'title')->get(),
            'tasks' => Task::with(['service:id,title', 'branch:id,name'])
                ->whereIn('status_id', $statusIds)
                ->get()
                ->map(function ($task) {
                    $serviceTitle = optional($task->service)->title->ka
                        ?? optional($task->service)->title->en
                        ?? 'Unnamed Service';

                    $branchName = optional($task->branch)->name ?? 'No Branch';

                    return [
                        'id' => $task->id,
                        'name' => "{$serviceTitle} ({$branchName})",
                    ];
                }),
        ];
    }

    /**
     * Validates and returns only the relations allowed by policy for a user.
     *
     * @param  User  $user  The user being created or updated
     * @param  array $data  The incoming validated request data
     * @return array        Relation keys that are allowed based on user role
     *
     * @throws ValidationException if unauthorized relations are included
     */
    protected function getAuthorizedRelations(User $user, array $data): array
    {
        $policy = App::make(UserConnectionPolicy::class);

        // Map of relation => input key
        $relationMap = [
            'companies' => 'company_ids',
            'branches' => 'branch_ids',
            'services' => 'service_ids',
            'tasks' => 'task_ids',
        ];

        $authorized = [];

        foreach ($relationMap as $relation => $inputKey) {
            if ($policy->canAttach($user, $relation)) {
                // Use actual input key if authorized
                $authorized[$relation] = $inputKey;
            } else {
                // Throw if unauthorized relation has submitted data
                if (!empty($data[$inputKey] ?? [])) {
                    throw ValidationException::withMessages([
                        $inputKey => "Role '{$user->getRoleName()}' cannot be assigned to {$relation}.",
                    ]);
                }

                // Mark relation as empty to clear any previous links
                $data[$inputKey] = []; // Ensure it's seen as empty by sync
                $authorized[$relation] = $inputKey;
            }
        }

        return $authorized;
    }

}

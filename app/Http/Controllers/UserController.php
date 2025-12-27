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
use App\Models\TaskOccurrenceStatus;
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
    protected array $modelRelations = [
        'role',
        'companies',
        'branches',
        'tasks',
        'tasks.service',
        'tasks.latestOccurrence',
        'tasks.latestOccurrence.status',
    ];
	protected array $localScopes = ['withoutAdmins'];



	public function show(User $user)
	{
		$role = $user->getRoleName();

		// Helper: eager load tasks with latest occurrence info
		$taskLoader = fn($q) => $q->with([
			'service:id,title',
			'branch:id,name,company_id',
			'branch.company:id,name',
			'latestOccurrence.status',
			'latestOccurrence.workers',
		])->latest()->take(5);

		// Limit hasMany eager loads per parent by trimming after load (SQL LIMIT is global)
		$trimTasks = function ($branches) {
			$branches->each(function ($branch) {
				if ($branch->relationLoaded('tasks')) {
					$branch->setRelation(
						'tasks',
						$branch->tasks->sortByDesc('created_at')->take(5)->values()
					);
				}
			});
		};

		$loaders = [
			'company_leader' => function ($user) use ($trimTasks) {
				$user->load([
					'role',
					'companies' => fn($q) => $q->latest()->take(5)->with([
						'economic_activity_type',
						'branches' => fn($bq) => $bq->latest()->take(5)->with([
							'tasks' => fn($tq) => $tq->with([
								'service:id,title',
								'branch:id,name,company_id',
								'branch.company:id,name',
								'latestOccurrence.status',
								'latestOccurrence.workers',
							]),
						]),
					]),
				]);

				$user->companies->each(function ($company) use ($trimTasks) {
					if ($company->relationLoaded('branches')) {
						$trimTasks($company->branches);
					}
				});
			},

			'responsible_person' => function ($user) use ($trimTasks) {
				$user->load([
					'role',
					'branches' => fn($q) => $q->latest()->take(5)->with([
						'company',
						'tasks' => fn($tq) => $tq->with([
							'service:id,title',
							'branch:id,name,company_id',
							'branch.company:id,name',
							'latestOccurrence.status',
							'latestOccurrence.workers',
						]),
					]),
				]);

				$trimTasks($user->branches);
			},
			'worker' => function ($user) use ($taskLoader) {
				$user->load([
					'role',
					'tasks' => $taskLoader,
				]);
			},
		];

		if (isset($loaders[$role])) {
			$loaders[$role]($user);
		} else {
			$user->load('role'); // fallback
		}

		return response()->json($user);
	}

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

		// Create the user first
		$user = $this->modelClass::create($data);

		try {
			// attempt to sync only allowed relations according to user role
			$authorizedRelations = $this->getAuthorizedRelations($user, $data);
			$this->syncRelations($user, $data, $authorizedRelations);
		} catch (\Throwable $e) {
			// Redirect to edit with error message because the user was created
			return redirect()
				->route("{$this->resourceName}.edit", $user->id)
				->withErrors([
					'redirectAlert' => 'მომხმარებელი ' . $user->name . 'შეიქმნა მაგრამ პრობლემის გამო მოხდა რედაქტირების გვერდზე გადამისამართება',
					'error' => $e->getMessage()
				]);
		}

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

		// Ensure policy checks reflect the incoming role during updates
		if (!empty($data['role_id']) && (int) $data['role_id'] !== (int) $user->role_id) {
			$user->role_id = $data['role_id'];
			$user->setRelation('role', Role::find($data['role_id']));
		}

		// Attempt to sync only allowed relations according to user role
		$authorizedRelations = $this->getAuthorizedRelations($user, $data);
		$this->syncRelations($user, $data, $authorizedRelations);

		$user->update($data);

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
		$assignableStatuses = ['pending', 'in_progress', 'on_hold'];
		$statusIds = TaskOccurrenceStatus::whereIn('name', $assignableStatuses)->pluck('id');
		
		return [
			'roles' => Role::withoutAdmins()->get(),
			'companies' => Company::select('id', 'name')->get(),
			'branches' => Branch::select('id', 'name')->get(),
			'services' => Service::select('id', 'title')->get(),
			'tasks' => Task::with(['service:id,title', 'branch:id,name', 'latestOccurrence.status'])
				->when($statusIds->isNotEmpty(), function ($query) use ($statusIds) {
					$query->whereHas('latestOccurrence', fn($q) => $q->whereIn('status_id', $statusIds));
				})
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
						$inputKey => "Role '{$user->role->display_name}' cannot be assigned to {$relation}.",
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

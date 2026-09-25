<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SyncsRelations;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\IncidentUserParticipant;
use App\Models\OrderUserParticipant;
use App\Models\Role;
use App\Models\Service;
use App\Models\Task;
use App\Models\TaskOccurrenceStatus;
use App\Models\TaskWorkerInvitation;
use App\Models\User;
use App\Policies\UserConnectionPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;

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
        'workerCompanies',
        'branches',
        'services',
        'tasks',
        'tasks.service',
        'tasks.latestOccurrence',
        'tasks.latestOccurrence.status',
    ];
	protected array $localScopes = ['withoutAdmins'];

	public function index(Request $request): View
	{
		$search = trim((string) $request->query('search'));
		$roleId = $this->positiveInteger($request->query('role_id'));
		$companyId = $this->positiveInteger($request->query('company_id'));

		$query = User::query()
			->withoutAdmins()
			->with($this->modelRelations)
			->when($search !== '', function ($query) use ($search) {
				$like = "%{$search}%";

				$query->where(function ($query) use ($like) {
					$query->where('full_name', 'LIKE', $like)
						->orWhere('email', 'LIKE', $like)
						->orWhere('phone', 'LIKE', $like)
						->orWhereHas('role', fn($role) => $role
							->where('display_name', 'LIKE', $like))
						->orWhereHas('companies', fn($company) => $company
							->where('name', 'LIKE', $like))
						->orWhereHas('workerCompanies', fn($company) => $company
							->where('name', 'LIKE', $like))
						->orWhereHas('branches', function ($branch) use ($like) {
							$branch->where('name', 'LIKE', $like)
								->orWhereHas('company', fn($company) => $company
									->where('name', 'LIKE', $like));
						});
				});
			})
			->when($roleId !== null, fn($query) => $query->where('role_id', $roleId))
			->when($companyId !== null, function ($query) use ($companyId) {
				$query->where(function ($query) use ($companyId) {
					$query->whereHas('companies', fn($company) => $company->whereKey($companyId))
						->orWhereHas('workerCompanies', fn($company) => $company->whereKey($companyId))
						->orWhereHas('branches', fn($branch) => $branch->where('company_id', $companyId));
				});
			})
			->orderByDesc($this->getOrderBy());

		return view('admin.users.index', [
			'users' => $query
				->paginate($this->perPage)
				->appends($request->query()),
			'userFilterOptions' => [
				'roles' => Role::query()
					->where('name', '!=', User::ADMIN_ROLE)
					->orderBy('display_name')
					->pluck('display_name', 'id')
					->all(),
				'companies' => Company::query()
					->orderBy('name')
					->pluck('name', 'id')
					->all(),
			],
			'resourceName' => $this->resourceName,
		]);
	}

	private function positiveInteger(mixed $value): ?int
	{
		return ctype_digit((string) $value) && (int) $value > 0 ? (int) $value : null;
	}

    /**
     * Compact, role-aware data for the administrator dashboard user modal.
     */
    public function dashboardSummary(User $user)
    {
        $user->load('role:id,name,display_name');
        $taskScope = $this->dashboardTaskScope($user);
        $activeTaskCount = (clone $taskScope)
            ->whereHas('latestOccurrenceWithoutVisibility.status', fn (Builder $query) => $query
                ->whereIn('name', Task::ACTIVE_OCCURRENCE_STATUSES))
            ->count();
        $completedTaskCount = (clone $taskScope)
            ->whereHas('latestOccurrenceWithoutVisibility.status', fn (Builder $query) => $query
                ->where('name', 'completed'))
            ->count();

        $tasks = (clone $taskScope)
            ->with([
                'branch.company:id,name',
                'service:id,title',
                'latestOccurrenceWithoutVisibility.status:id,name,display_name',
            ])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(function (Task $task): array {
                $occurrence = $task->latestOccurrenceWithoutVisibility;

                return [
                    'id' => $task->id,
                    'service' => $task->service_name_snapshot
                        ?: data_get($task->service?->title, 'ka')
                        ?: data_get($task->service?->title, 'en')
                        ?: '—',
                    'branch' => $task->branch?->name ?: $task->branch_name_snapshot ?: '—',
                    'company' => $task->branch?->company?->name ?: '—',
                    'status' => $occurrence?->status?->name ?: 'unknown',
                    'status_label' => $occurrence?->status?->display_name ?: 'უცნობი',
                    'due_date' => $occurrence?->due_date?->format('d.m.Y') ?: null,
                    'updated_at' => $task->updated_at?->format('d.m.Y H:i') ?: null,
                    'occurrences_url' => route('tasks.index', [
                        'occurrences_task_id' => $task->id,
                    ]),
                ];
            })
            ->values();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => (bool) $user->is_active,
                'created_at' => $user->created_at?->format('d.m.Y'),
                'role' => [
                    'name' => $user->role?->name ?? 'unknown',
                    'display_name' => $user->role?->display_name ?? 'როლი უცნობია',
                ],
            ],
            'stats' => $this->dashboardStats($user, $activeTaskCount, $completedTaskCount),
            'connections' => $this->dashboardConnections($user),
            'tasks' => $tasks,
            'links' => [
                'edit_user' => route('users.edit', $user),
                'tasks' => route('tasks.index', ['filter' => ['search' => $user->full_name]]),
            ],
        ]);
    }

    private function dashboardTaskScope(User $user): Builder
    {
        return match ($user->getRoleName()) {
            'worker' => Task::query()->whereHas(
                'users',
                fn (Builder $query) => $query->whereKey($user->id)
            ),
            'company_leader' => Task::query()->whereHas(
                'branch.company.users',
                fn (Builder $query) => $query->whereKey($user->id)
            ),
            'responsible_person' => Task::query()->whereHas(
                'branch.users',
                fn (Builder $query) => $query->whereKey($user->id)
            ),
            default => Task::query()->whereRaw('1 = 0'),
        };
    }

    /**
     * @return array<int, array{label: string, value: int, icon: string, tone: string}>
     */
    private function dashboardStats(User $user, int $activeTaskCount, int $completedTaskCount): array
    {
        $role = $user->getRoleName();
        $unsignedDocuments = IncidentUserParticipant::query()
            ->where('user_id', $user->id)
            ->whereNull('signed_at')
            ->count()
            + OrderUserParticipant::query()
                ->where('user_id', $user->id)
                ->whereNull('signed_at')
                ->count();

        $stat = fn (string $label, int $value, string $icon, string $tone): array => compact(
            'label',
            'value',
            'icon',
            'tone'
        );

        return match ($role) {
            'worker' => [
                $stat('აქტიური საქმეები', $activeTaskCount, 'bi-list-check', 'primary'),
                $stat('დასრულებული საქმეები', $completedTaskCount, 'bi-check2-circle', 'success'),
                $stat('მოლოდინში მოწვევები', TaskWorkerInvitation::query()
                    ->where('invited_worker_id', $user->id)
                    ->where('status', TaskWorkerInvitation::STATUS_PENDING)
                    ->count(), 'bi-person-plus', 'warning'),
                $stat('დაკავშირებული კომპანიები', $user->workerCompanies()->count(), 'bi-buildings', 'secondary'),
            ],
            'company_leader' => [
                $stat('აქტიური საქმეები', $activeTaskCount, 'bi-list-check', 'primary'),
                $stat('დასრულებული საქმეები', $completedTaskCount, 'bi-check2-circle', 'success'),
                $stat('ხელმოწერის მოლოდინში', $unsignedDocuments, 'bi-pen', 'warning'),
                $stat('კომპანიები', $user->companies()->count(), 'bi-buildings', 'secondary'),
            ],
            'responsible_person' => [
                $stat('აქტიური საქმეები', $activeTaskCount, 'bi-list-check', 'primary'),
                $stat('დასრულებული საქმეები', $completedTaskCount, 'bi-check2-circle', 'success'),
                $stat('ხელმოწერის მოლოდინში', $unsignedDocuments, 'bi-pen', 'warning'),
                $stat('ფილიალები', $user->branches()->count(), 'bi-diagram-3', 'secondary'),
                $stat('სერვისები', $user->services()->count(), 'bi-briefcase', 'info'),
            ],
            default => [],
        };
    }

    /**
     * @return array{label: string, total: int, items: array<int, string>}
     */
    private function dashboardConnections(User $user): array
    {
        return match ($user->getRoleName()) {
            'company_leader' => [
                'label' => 'დაკავშირებული კომპანიები',
                'total' => $user->companies()->count(),
                'items' => $user->companies()->orderBy('name')->limit(6)->pluck('name')->all(),
            ],
            'responsible_person' => [
                'label' => 'დაკავშირებული ფილიალები',
                'total' => $user->branches()->count(),
                'items' => $user->branches()
                    ->with('company:id,name')
                    ->orderBy('name')
                    ->limit(6)
                    ->get()
                    ->map(fn (Branch $branch) => "{$branch->name} — " . ($branch->company?->name ?? '—'))
                    ->all(),
            ],
            'worker' => [
                'label' => 'საქმის შექმნის კომპანიები',
                'total' => $user->workerCompanies()->count(),
                'items' => $user->workerCompanies()->orderBy('name')->limit(6)->pluck('name')->all(),
            ],
            default => ['label' => 'კავშირები', 'total' => 0, 'items' => []],
        };
    }



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
			'latestOccurrenceWithoutVisibility.status',
			'latestOccurrenceWithoutVisibility.workers',
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
								'latestOccurrenceWithoutVisibility.status',
								'latestOccurrenceWithoutVisibility.workers',
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
							'latestOccurrenceWithoutVisibility.status',
							'latestOccurrenceWithoutVisibility.workers',
						]),
					]),
				]);

				$trimTasks($user->branches);
			},
			'worker' => function ($user) use ($taskLoader) {
				$user->load([
					'role',
					'workerCompanies',
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
						?? $task->service_name_snapshot
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
			'workerCompanies' => 'worker_company_ids',
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkerTaskRequest;
use App\Models\Branch;
use App\Models\Service;
use App\Models\Task;
use App\Services\Tasks\TaskCreator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class WorkerTaskController extends Controller
{
    public function __construct(
        private TaskCreator $taskCreator
    ) {
    }

    public function create(): View
    {
        $this->authorize('create', Task::class);

        $services = Service::query()
            ->get()
            ->mapWithKeys(fn(Service $service) => [
                $service->id => $service->title->ka ?? $service->title->en,
            ])
            ->toArray();

        return view('management.worker.tasks.create', [
            'services' => $services,
            'branches' => Branch::query()->pluck('name', 'id')->toArray(),
            'sidebarItems' => config('sidebar.worker'),
        ]);
    }

    public function store(StoreWorkerTaskRequest $request): RedirectResponse
    {
        $data = $this->prepareTaskData($request->validated());
        $data['created_by_user_id'] = $request->user()->id;
        $data['user_ids'] = [$request->user()->id];
        $data['visibility'] = '1';
        $data['archived'] = '0';

        if (!($data['is_recurring'] ?? false)) {
            $data['recurrence_interval'] = null;
        }

        try {
            $this->taskCreator->createWithInitialOccurrence($data);
        } catch (Throwable $e) {
            Log::error('Worker task creation failed', [
                'worker_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'task_creation' => 'სამუშაოს შექმნა ვერ მოხერხდა. გთხოვთ, სცადოთ თავიდან.',
                ]);
        }

        return redirect()
            ->route('management.dashboard.tasks')
            ->with('success', 'სამუშაო შეიქმნა და თქვენ ავტომატურად მიენიჭეთ.');
    }

    private function prepareTaskData(array $data): array
    {
        $branch = Branch::query()->findOrFail($data['branch_id']);
        $data['branch_name_snapshot'] = $branch->name;

        $temporaryServiceName = trim((string) ($data['temporary_service_name'] ?? ''));

        if ($temporaryServiceName !== '') {
            $data['service_id'] = null;
            $data['service_name_snapshot'] = $temporaryServiceName;
        } else {
            $service = Service::query()->findOrFail($data['service_id']);
            $data['service_name_snapshot'] = $service->title->ka ?? $service->title->en;
        }

        unset($data['service_mode'], $data['temporary_service_name']);

        return $data;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRouterController extends Controller
{
    /**
     * Redirect to management dashboard.
     */
    public function redirect()
    {
        return redirect()->route('management.dashboard.page');
    }

    /**
     * Dynamically call the controller method based on user role and action.
     */
    protected function resolveAndCall(Request $request, string $method)
    {
        // Controllers based on user role
        $roleControllers = [
            'company_leader' => 'App\Http\Controllers\CompanyLeaderController',
            'responsible_person' => 'App\Http\Controllers\ResponsiblePersonController',
            'worker' => 'App\Http\Controllers\WorkerController',
        ];

        $role = $request->user()->getRoleName(); // user role:str

        // Skip certain roles for specific methods
        if ($method === 'displayTasks' && $role === 'worker') {
            abort(403);
        }

        $controller = $roleControllers[$role] ?? null;

        // Abort if controller or method does not exist
        if (!$controller || !method_exists(app($controller), $method)) {
            abort(403);
        }

        // Call the proper controller method
        return app()->call("{$controller}@{$method}");
    }

    /**
     * Route to correct dashboard based on user role.
     */
    public function redirectDashboard(Request $request)
    {
        return $this->resolveAndCall($request, 'displayDashboard');
    }

    /**
     * Route to correct task list based on user role.
     */
    public function redirectTask(Request $request)
    {
        return $this->resolveAndCall($request, 'displayTasks');
    }
}

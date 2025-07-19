<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardRouterController extends Controller
{

    /**
     * Redirect to dashboard
     */
    public function redirect()
    {
        return redirect()->route('management.dashboard.page');
    }

    /**
     * Handle witch controller to use based on user role
     */
    public function handle(Request $request)
    {
        switch ($request->user()->getRoleName()) {
            case 'company_leader':
                return app()->call('App\Http\Controllers\CompanyLeaderController@displayDashboard');
            case 'responsible_person':
                return app()->call('App\Http\Controllers\ResponsiblePersonController@displayDashboard');
            // case 'worker':
            //     return app()->call('App\Http\Controllers\CompanyLeaderDashboardController@show');
            default:
                abort(403);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyLeaderController extends Controller
{

    public function displayDashboard()
    {
        $sidebarItems = config('sidebar.company-leader');
        return view('management.company-leader.dashboard', compact('sidebarItems'));
    }
}

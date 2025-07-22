<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function displayDashboard()
    {
        $sidebarItems = config('sidebar.worker');
        return view('management.worker.dashboard', compact('sidebarItems'));
    }
}

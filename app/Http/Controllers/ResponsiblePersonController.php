<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponsiblePersonController extends Controller
{
    public function displayDashboard()
    {
        $sidebarItems = config('sidebar.responsible-person');
        return view('management.responsible-person.dashboard', compact('sidebarItems'));
    }
}

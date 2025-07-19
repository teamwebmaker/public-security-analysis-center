<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyLeaderController extends Controller
{
    public function displayDashboard()
    {
        return view('management.company-leader.dashboard');
    }
}

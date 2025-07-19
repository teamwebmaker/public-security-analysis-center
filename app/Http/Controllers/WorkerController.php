<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkerController extends Controller
{
    public function displayDashboard()
    {
        return view('management.worker.dashboard');
    }
}

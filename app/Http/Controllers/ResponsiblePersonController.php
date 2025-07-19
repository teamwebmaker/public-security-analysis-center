<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponsiblePersonController extends Controller
{
    public function displayDashboard()
    {
        return view('management.responsible-person.dashboard');
    }
}

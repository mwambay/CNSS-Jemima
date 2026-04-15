<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class EmployerInterfaceController extends Controller
{
    public function index(): View
    {
        return view('employers.index');
    }
}

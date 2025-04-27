<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Display the FAQ page for employees
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('employee.support.index');
    }
}

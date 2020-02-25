<?php

namespace App\Http\Controllers;

use App\Helpers\Employee;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $data = $this->employee->all();

        if($data['status'] == 'error') {
            return view('error');
        }

        $employees = $this->employee->paginate($request, $data['data']);
        return view('employees', compact('employees'));
    }



}
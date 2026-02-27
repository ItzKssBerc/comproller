<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeIdCardController extends Controller
{
    public function show(Employee $employee)
    {
        return view('filament.hr.components.id-card-print', [
            'record' => $employee,
            'isStandalone' => true,
        ]);
    }

    public function bulkShow(Request $request)
    {
        $employeeIds = $request->input('ids', []);
        $employees = Employee::whereIn('id', $employeeIds)->get();

        return view('filament.hr.pages.bulk-id-card-print', [
            'employees' => $employees,
            'isStandalone' => true,
        ]);
    }
}

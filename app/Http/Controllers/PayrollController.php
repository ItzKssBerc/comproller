<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class PayrollController extends Controller
{
    use \App\Traits\HasPayrollPdfData;

    public function download(Request $request, Payroll $payroll): Response
    {
        $locale = $request->query('locale', 'hu');
        $dualCopy = $request->boolean('dual_copy', false);
        App::setLocale($locale);

        $data = $this->preparePayrollData($payroll);
        $data['dualCopy'] = $dualCopy;

        $pdf = Pdf::loadView('pdf.payslip', $data);

        return $pdf->download('payslip-'.$payroll->period.'-'.$data['employee']->personalData?->last_name.'.pdf');
    }

    public function bulkDownload(Request $request): Response
    {
        $ids = $request->input('ids', []);
        $locale = $request->query('locale', 'hu');
        $dualCopy = $request->boolean('dual_copy', false);
        App::setLocale($locale);

        $payrolls = Payroll::whereIn('id', $ids)->get();
        $payrollsData = $payrolls->map(fn (Payroll $p) => array_merge($this->preparePayrollData($p), ['dualCopy' => $dualCopy]));

        $pdf = Pdf::loadView('pdf.bulk-payslip', [
            'payrollsData' => $payrollsData,
        ]);

        return $pdf->download('bulk-payslips-'.now()->format('Y-m-d').'.pdf');
    }
}

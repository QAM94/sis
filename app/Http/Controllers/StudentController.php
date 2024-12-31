<?php

namespace App\Http\Controllers;

use App\Models\OfferedCourse;
use App\Models\PaymentVoucher;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function downloadPaymentVoucher(PaymentVoucher $voucher)
    {
        $pdf = Pdf::loadView('vouchers.payment-voucher', ['voucher' => $voucher]);

        // Return the PDF as a download
        return $pdf->download("payment-voucher-{$voucher->id}.pdf");
    }
}

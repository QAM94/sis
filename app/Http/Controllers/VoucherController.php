<?php

namespace App\Http\Controllers;

use App\Models\PaymentVoucher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function downloadPaymentVoucher(PaymentVoucher $voucher)
    {
        $pdf = Pdf::loadView('vouchers.payment-voucher', ['voucher' => $voucher]);

        // Return the PDF as a download
        return $pdf->download("payment-voucher-{$voucher->id}.pdf");
    }
}

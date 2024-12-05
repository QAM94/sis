<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoucherController;

Route::get('/voucher/{voucher}/download', [VoucherController::class, 'downloadPaymentVoucher'])
    ->name('voucher.download');


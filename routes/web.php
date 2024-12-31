<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/voucher/{voucher}/download', [StudentController::class, 'downloadPaymentVoucher'])
    ->name('voucher.download');


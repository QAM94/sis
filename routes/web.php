<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/voucher/{voucher}/download', [StudentController::class, 'downloadFeeVoucher'])
    ->name('voucher.download');
Route::get('/course-timings/{id}/download', [StudentController::class, 'downloadAttendanceSheet'])
    ->name('attendance_sheet.download');


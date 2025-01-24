<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/voucher/{voucher}/download', [StudentController::class, 'downloadFeeVoucher'])
    ->name('voucher.download');
Route::get('/course-timings/{id}/attendance', [StudentController::class, 'downloadAttendanceSheet'])
    ->name('attendance_sheet.download');
Route::get('/course-timings/{id}/grades', [StudentController::class, 'downloadGradeSheet'])
    ->name('grade_sheet.download');
Route::post('/grades/upload/{schedule_id}', [StudentController::class, 'uploadGradeSheet'])
    ->name('grade_sheet.upload');


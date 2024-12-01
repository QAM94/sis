<?php

use Illuminate\Support\Facades\Route;
use App\Filament\Resources\StudentEnrollmentResource\Pages\CourseRegistration;
use App\Filament\Resources\OfferedCourseResource\Pages\OfferedCourses;

Route::get('/registration', [OfferedCourses::class, 'getCourses'])->name('register.courses');
Route::post('/enroll-course/{course}', [CourseRegistration::class, 'enrollInCourse'])->name('enroll.course');
Route::delete('/drop-course/{course}', [CourseRegistration::class, 'dropCourse'])->name('drop.course');


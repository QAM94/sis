<?php

namespace App\Filament\Resources\StudentEnrollmentResource\Pages;

use App\Filament\Resources\StudentEnrollmentResource;
use Filament\Resources\Pages\Page;

class CourseRegistration extends Page
{
    protected static string $resource = StudentEnrollmentResource::class;

    protected static string $view = 'filament.resources.student-enrollment-resource.pages.course-registration';

}

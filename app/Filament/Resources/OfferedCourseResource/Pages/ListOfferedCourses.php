<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
use App\Models\Semester;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOfferedCourses extends ListRecords
{
    protected static string $resource = OfferedCourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(fn () => route('filament.admin.resources.offered-courses.create',
                    ['semester_id' => request()->get('semester_id')]))
                ->mutateFormDataUsing(fn (array $data) => array_merge($data,
                    ['semester_id' => request()->get('semester_id')])),
        ];
    }

    public function getTitle(): string
    {
        $semesterId = request()->get('semester_id');
        $semester = Semester::find($semesterId);
        if(!empty($semester)) {
            return "Offered Courses for: $semester->type $semester->year";
        }
        return "Offered Courses";
    }
}

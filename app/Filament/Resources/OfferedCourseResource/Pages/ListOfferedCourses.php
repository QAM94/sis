<?php

namespace App\Filament\Resources\OfferedCourseResource\Pages;

use App\Filament\Resources\OfferedCourseResource;
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
                    ['program_id' => request()->get('program_id')]))
                ->mutateFormDataUsing(fn (array $data) => array_merge($data, ['program_id' => request()->get('program_id')])),
        ];
    }

    public function getTitle(): string
    {
        $programId = request()->get('program_id');
        $programTitle = \App\Models\Program::find($programId)?->title ?? 'All Programs';

        return "Offered Courses for: {$programTitle}";
    }
}

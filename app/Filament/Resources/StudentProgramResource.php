<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentProgramResource\Pages;
use App\Models\OfferedCourse;
use App\Models\StudentProgram;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentProgramResource extends Resource
{
    protected static ?string $model = StudentProgram::class;

    protected static ?string $module = 'student_program';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'My Programs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.type')
                    ->label('Program Type')->sortable()->searchable()
                    ->default('No Program Assigned'),
                Tables\Columns\TextColumn::make('program.title')
                    ->label('Program Name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Active',
                        'warning' => 'Pending',
                        'danger' => 'Inactive',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('register_courses')
                    ->label('Register Courses')
                    ->icon('heroicon-o-document-text')
                    ->url(function ($record) {
                        return route('filament.admin.resources.offered-courses.register',
                            ['program_id' => $record->program_id]);
                    })
                    ->visible(function ($record) {
                        // Show button only if voucher exists
                        $offeredCount = OfferedCourse::courseCount($record->program_id);
                        if ($offeredCount > 0) {
                            return 1;
                        }
                        return 0;
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentPrograms::route('/'),
        ];
    }
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('student');
    }
}

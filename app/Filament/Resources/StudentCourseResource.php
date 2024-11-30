<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentCourseResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\StudentCourse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentCourseResource extends Resource
{
    use HasResourcePermissions;
    protected static ?string $model = StudentCourse::class;
    protected static ?string $module = 'student_course';
    protected static bool $shouldRegisterNavigation = false;



    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'name') // Assuming 'name' exists
                    ->required(),
                Forms\Components\Select::make('offered_course_id')
                    ->relationship('offeredCourse', 'programCourse.course.title')
                    ->required(),
                Forms\Components\TextInput::make('semester')
                    ->numeric()
                    ->nullable(),
                Forms\Components\DatePicker::make('enrolled_at')
                    ->default(now())
                    ->required(),
                Forms\Components\DatePicker::make('dropped_at')->nullable(),
                Forms\Components\Select::make('status')
                    ->options(['Enrolled' => 'Enrolled', 'Dropped' => 'Dropped'])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')->label('Student'),
                Tables\Columns\TextColumn::make('offeredCourse.programCourse.course.title')->label('Course'),
                Tables\Columns\TextColumn::make('semester')->label('Semester'),
                Tables\Columns\TextColumn::make('enrolled_at')->label('Enrolled At')->date(),
                Tables\Columns\TextColumn::make('status')->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('Drop')
                    ->action(fn (StudentCourse $record) => $record->update([
                        'status' => 'Dropped',
                        'dropped_at' => now(),
                    ]))
                    ->requiresConfirmation()
                    ->label('Drop')
                    ->hidden(fn (StudentCourse $record) => $record->status === 'Dropped'),
            ]);
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
            'index' => Pages\ListStudentCourses::route('/'),
            'create' => Pages\CreateStudentCourse::route('/create'),
            'edit' => Pages\EditStudentCourse::route('/{record}/edit'),
        ];
    }
}

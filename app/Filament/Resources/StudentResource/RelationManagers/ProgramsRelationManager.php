<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\OfferedCourse;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;

class ProgramsRelationManager extends RelationManager
{
    protected static string $relationship = 'studentPrograms';

    protected static ?string $recordTitleAttribute = 'program_id'; // Or any suitable attribute

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'title') // Assuming the Program model has a `name` attribute
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pre-Enrollment' => 'Pre-Enrollment',
                        'Enrolled' => 'Enrolled',
                        'Graduated' => 'Graduated',
                        'Suspended' => 'Suspended',
                        'Withdrawn' => 'Withdrawn',
                        'Deferred' => 'Deferred',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('enrolled_on')
                    ->label('Enrolled On')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.title')->label('Program Name'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
                Tables\Columns\TextColumn::make('enrolled_on')
                    ->label('Enrolled On')
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Pre-Enrollment' => 'Pre-Enrollment',
                        'Enrolled' => 'Enrolled',
                        'Graduated' => 'Graduated',
                        'Suspended' => 'Suspended',
                        'Withdrawn' => 'Withdrawn',
                        'Deferred' => 'Deferred',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('generate_transcript')
                    ->label('Generate Transcript')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->action(function ($record) {
                        // Call the generate transcript logic
                        $filePath = storage_path("app/public/transcripts/{$record->id}_transcript.pdf");

                        // Check if the transcript already exists, delete if needed
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        // Generate the PDF using the data
                        $pdf = Pdf::loadView('pdf.transcript', [
                            'studentProgram' => $record,
                            'student' => $record->student,
                        ]);

                        // Save the PDF to the specified file path
                        $pdf->save($filePath);

                        // Immediately return a download response
                        return response()->download($filePath, "{$record->id}_transcript.pdf");
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Transcript')
                    ->modalSubheading('Are you sure you want to generate the transcript for this course?'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }


}

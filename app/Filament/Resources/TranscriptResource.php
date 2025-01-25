<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranscriptResource\Pages;
use App\Imports\ClassGradesImport;
use App\Models\OfferedCourse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Maatwebsite\Excel\Facades\Excel;

class TranscriptResource extends Resource
{
    protected static ?string $model = OfferedCourse::class;

    protected static ?string $pluralLabel = 'Transcripts';
    protected static ?int $navigationSort = 2; // Adjust as needed for menu position

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Columns\TextColumn::make('semester')
                    ->label('Semester')
                    ->formatStateUsing(fn ($record) => "{$record->semester->type} {$record->semester->year}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.title')->label('Course'),
                Tables\Columns\TextColumn::make('course.crn')->label('Code'),
                Tables\Columns\TextColumn::make('instructor.full_name')->label('Instructor'),
                Tables\Columns\TextColumn::make('studentCount')->label('Students'),
                Tables\Columns\TextColumn::make('updated_at')->label('Last Updated')
                    ->dateTime()
            ])
            ->filters([
                Tables\Filters\Filter::make('completed')
                    ->label('Completed')
                    ->query(fn ($query) => $query->where('status', 'Completed')),
                Tables\Filters\Filter::make('scheduled')
                    ->label('Scheduled')
                    ->query(fn ($query) => $query->where('status', 'Scheduled'))
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('warning'),

                    // Download Grade Sheet
                    Tables\Actions\Action::make('grade_sheet')
                        ->label('Download Grade Sheet')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(function ($record) {
                            return route('grade_sheet.download', ['id' => $record->id]);
                        })
                        ->color('info')
                        ->openUrlInNewTab(),

                    // Upload Grade Sheet
                    Tables\Actions\Action::make('upload_grade_sheet')
                        ->label('Upload Grade Sheet')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('default')
                        ->visible(fn($record): bool => $record->status === 'Scheduled')
                        ->form([
                            Forms\Components\FileUpload::make('grade_sheet')
                                ->label('Upload Grade Sheet')
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                ->required()->directory('grade_sheets'),
                        ])
                        ->action(function (array $data, $record) {
                            // Determine if the file is stored in the public directory
                            $filePath = public_path("storage/{$data['grade_sheet']}"); // For public storage

                            // Ensure the file exists before processing
                            if (!file_exists($filePath)) {
                                Notification::make()
                                    ->title('File Not Found')
                                    ->danger()
                                    ->send();
                            }
                            try {
                                // Call your existing import logic
                                Excel::import(new ClassGradesImport($record->id), $filePath);
                                return Notification::make()
                                    ->title('Grade Sheet Uploaded')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                return redirect()->back()->with('error', 'Failed to import grades: ' . $e->getMessage());
                            }
                        }),

                    // Add "Mark as Completed" Action
                    Tables\Actions\Action::make('mark_as_completed')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($record) {
                            // Update status to Completed
                            $record->update(['status' => 'Completed']);

                            Notification::make()
                                ->title('Course Marked as Completed')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->status === 'Scheduled'), // Only show if not Scheduled
                ])->button()->label('Actions')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTranscripts::route('/'),
            'view' => Pages\ViewTranscript::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['Scheduled', 'Completed'])
            ->orderBy('created_at', 'DESC');
    }
}

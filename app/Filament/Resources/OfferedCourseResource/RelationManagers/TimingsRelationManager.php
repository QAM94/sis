<?php

namespace App\Filament\Resources\OfferedCourseResource\RelationManagers;

use App\Models\CourseTiming;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TimingsRelationManager extends RelationManager
{
    protected static string $relationship = 'timings';
    protected static ?string $model = CourseTiming::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('day')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->multiple()
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('day')
            ->columns([
                Tables\Columns\TextColumn::make('day')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Start Time')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('End Time')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data): Model {
                        $days = is_array($data['day']) ? $data['day'] : [$data['day']];

                        foreach ($days as $day) {
                            $rec = CourseTiming::create([
                                'offered_course_id' => $this->ownerRecord->id, // Reference parent
                                'day' => $day,
                                'start_time' => $data['start_time'],
                                'end_time' => $data['end_time'],
                            ]);
                        }
                        return $rec;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(function (Tables\Actions\EditAction $action): array {
                        return [
                            Forms\Components\Select::make('day')
                                ->options([
                                    'Monday' => 'Monday',
                                    'Tuesday' => 'Tuesday',
                                    'Wednesday' => 'Wednesday',
                                    'Thursday' => 'Thursday',
                                    'Friday' => 'Friday',
                                    'Saturday' => 'Saturday',
                                    'Sunday' => 'Sunday',
                                ])
                                ->multiple(false) // Ensure single day selection during editing
                                ->required(),
                            Forms\Components\TimePicker::make('start_time')->required(),
                            Forms\Components\TimePicker::make('end_time')->required(),
                        ];
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /*protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract the days from the form data
        $days = $data['day'];
        unset($data['day']); // Remove `day` to avoid saving it as JSON

        foreach ($days as $day) {
            // Create a separate record for each day
            \App\Models\CourseTiming::create([
                'offered_course_id' => $data['offered_course_id'], // Replace with actual offered_course_id
                'day' => $day,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);
        }

        // Prevent saving in the default way since we manually created records
        return [];
    }*/
}

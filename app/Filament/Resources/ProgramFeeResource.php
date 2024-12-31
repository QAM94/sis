<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramFeeResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\ProgramFee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgramFeeResource extends Resource
{
    use HasResourcePermissions;
    protected static ?string $model = ProgramFee::class;
    protected static ?string $module = 'program_fee';
    protected static ?string $currency = 'cad';
    protected static ?string $navigationGroup = 'Fees Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('program_id')
                    ->default(fn() => request()->get('program_id')) // Dynamically set default from URL
                    ->required(),
                Forms\Components\Select::make('program')
                    ->relationship('program', 'title')
                    ->default(fn() => request()->get('program_id')) // Dynamically set default from URL
                    ->label('Program')
                    ->disabled(),

                Forms\Components\TextInput::make('admission_fee')
                    ->label('Admission Fee')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('security_deposit')
                    ->label('Security Deposit')
                    ->numeric()
                    ->required(),

                Forms\Components\Select::make('fee_by')
                    ->label('Semester Fee By')
                    ->options([
                        'course' => 'Per Course',
                        'credit' => 'Per Credit',
                        'semester' => 'Per Semester',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('reg_fee')
                    ->label('Registration Fee')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('tution_fee')
                    ->label('Tuition Fee')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('transport_fee')
                    ->label('Transport Fee')
                    ->numeric()
                    ->required(),

                Forms\Components\TextInput::make('other_fee')
                    ->label('Other Fee')
                    ->numeric()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('program.title')->label('Program'),
                Tables\Columns\TextColumn::make('admission_fee')->label('Admission Fee')
                    ->money(static::$currency),
                Tables\Columns\TextColumn::make('security_deposit')->label('Security Deposit')
                    ->money(static::$currency),
                Tables\Columns\TextColumn::make('reg_fee')->label('Registration Fee')
                    ->money(static::$currency),
                Tables\Columns\TextColumn::make('tution_fee')->label('Tuition Fee')
                    ->money(static::$currency),
                Tables\Columns\TextColumn::make('transport_fee')->label('Transport Fee')
                    ->money(static::$currency),
                Tables\Columns\TextColumn::make('other_fee')->label('Other Fee')
                    ->money(static::$currency),
                Tables\Columns\TextColumn::make('fee_by')->label('Fee By'),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'title'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListProgramFees::route('/'),
            'create' => Pages\CreateProgramFee::route('/create'),
            'edit' => Pages\EditProgramFee::route('/{record}/edit'),
        ];
    }
}

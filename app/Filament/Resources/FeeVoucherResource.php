<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeVoucherResource\Pages;
use App\Filament\Traits\HasResourcePermissions;
use App\Models\FeeVoucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FeeVoucherResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = FeeVoucher::class;
    protected static ?string $module = 'fee_voucher';

    protected static ?string $navigationGroup = 'Fees Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('voucher_number')
                    ->label('Voucher Number')
                    ->disabled(),

                Forms\Components\TextInput::make('total_amount')
                    ->label('Total Amount')
                    ->disabled(),

                Forms\Components\Textarea::make('fee_breakdown')
                    ->label('Fee Breakdown')
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Confirmed' => 'Confirmed',
                        'Rejected' => 'Rejected',
                    ]),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),

                Forms\Components\FileUpload::make('payment_proof')
                    ->label('Payment Proof')
                    ->directory('payment_proofs')
                    ->required()
                    ->visible(fn ($record) => $record->status === 'Pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('voucher_number')->label('Voucher Number'),
                Tables\Columns\TextColumn::make('total_amount')->label('Total Amount'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'Pending',
                        'success' => 'Confirmed',
                        'danger' => 'Rejected',
                    ]),
                Tables\Columns\TextColumn::make('payment_date')->label('Payment Date')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('uploadProof')
                    ->label(fn ($record) => $record->payment_proof ? 'View Proof' : 'Upload Proof')
                    ->icon(fn ($record) => $record->payment_proof ? 'heroicon-o-eye' : 'heroicon-o-arrow-up-on-square')
                    ->url(fn ($record) => $record->payment_proof ? asset('storage/' . $record->payment_proof) : null, true) // Open proof in a new tab if it exists
                    ->form(fn ($record) => !$record->payment_proof ? [
                        Forms\Components\FileUpload::make('payment_proof')
                            ->label('Payment Proof')
                            ->directory('payment_proofs')
                            ->required(),
                    ] : [])
                    ->action(function ($record, $data) {
                        if (!$record->payment_proof) {
                            $record->payment_proof = $data['payment_proof'];
                            $record->status = 'Pending'; // Update status to Pending after upload
                            $record->save();
                        }
                    })
                    ->requiresConfirmation(fn ($record) => !$record->payment_proof), // Confirm only for uploads
        Tables\Actions\Action::make('download_voucher')
                    ->label('Download Voucher')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->url(function ($record) {
                        return route('voucher.download', ['voucher' => $record->id]); // Route to handle download

                    }),
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
            'index' => Pages\ListFeeVouchers::route('/'),
            'create' => Pages\CreateFeeVoucher::route('/create'),
            'edit' => Pages\EditFeeVoucher::route('/{record}/edit'),
        ];
    }
}

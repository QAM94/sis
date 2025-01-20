<?php

namespace App\Filament\Resources\FeeVoucherResource\Pages;

use App\Filament\Resources\FeeVoucherResource;
use App\Models\OfferedCourse;
use App\Models\FeeVoucher;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListFeeVouchers extends ListRecords
{
    protected static string $resource = FeeVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        $student = Auth::user()->student ?? null;
        if (!empty($student)) {
            return FeeVoucher::query()->whereHas('studentEnrollment', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            });
        }
        return FeeVoucher::query();
    }
}

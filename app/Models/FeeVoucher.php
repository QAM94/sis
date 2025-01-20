<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeVoucher extends Model
{
    protected $fillable = ['student_enrollment_id', 'voucher_number', 'total_amount', 'fee_breakdown',
        'status', 'payment_date', 'payment_proof'];

    public $appends = ['student', 'semester'];

    public function getStudentAttribute()
    {
        return $this->studentEnrollment->student;
    }

    public function getSemesterAttribute()
    {
        return $this->studentEnrollment->semester;
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }
}

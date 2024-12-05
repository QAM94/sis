<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentVoucher extends Model
{
    protected $fillable = ['enrollment_id', 'total_amount', 'fee_breakdown', 'status'];

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
        return $this->belongsTo(StudentEnrollment::class, 'enrollment_id');
    }
}

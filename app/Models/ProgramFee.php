<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramFee extends Model
{
    use SoftDeletes;

    protected $fillable = ['program_id', 'admission_fee', 'security_deposit', 'reg_fee',
        'tution_fee', 'transport_fee', 'other_fee', 'fee_by'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public static function getSemesterFee($student, $enrolledCourses)
    {
        $tutionFee = $totalFee = 0;
        $programFees = self::where('program_id', $student->program_id)->first();

        switch ($programFees->fee_by) {
            case 'course':
                $tutionFee = count($enrolledCourses) * $programFees->tution_fee;
                break;

            case 'credit':
                $totalCredits = $enrolledCourses->sum(function ($enrolledCourse) {
                    return $enrolledCourse->offeredCourse->programCourse->credits;
                });
                $extraCredits = $enrolledCourses->sum(function ($enrolledCourse) {
                    return $enrolledCourse->offeredCourse->programCourse->credits_extra;
                });
                $tutionFee = ($totalCredits + $extraCredits) * $programFees->tution_fee;
                break;

            case 'semester':
                $tutionFee = $programFees->tution_fee;
                break;
        }

        $prevCount = PaymentVoucher::whereHas('studentEnrollment', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->where('status', 'paid')->count();

        if($prevCount > 0){
            $admission_fee = 0;
            $security_deposit = 0;
        }
        else {
            $admission_fee = $programFees->admission_fee;
            $security_deposit = $programFees->security_deposit;
        }

        // Add other applicable fees
        $totalFee = $tutionFee;
        $totalFee += $admission_fee;
        $totalFee += $security_deposit;
        $totalFee += $programFees->reg_fee;
        $totalFee += $programFees->transport_fee;
        $totalFee += $programFees->other_fee;

        return ['total' => $totalFee, 'breakdown' => [
            'admission_fee' => $admission_fee,
            'security_deposit' => $security_deposit,
            'reg_fee' => $programFees->reg_fee,
            'tution_fee' => $tutionFee,
            'transport_fee' => $programFees->transport_fee,
            'other_fee' => $programFees->other_fee
        ]];
    }
}

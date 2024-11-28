<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramFee extends Model
{
    use SoftDeletes;

    protected $fillable = ['program_id', 'admission_fee', 'security_deposit', 'reg_fee',
        'tution_fee', 'transport_fee', 'other_fee', 'fee_by'];
}

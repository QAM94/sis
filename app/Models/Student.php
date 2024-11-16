<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    protected $fillable = ['user_id', 'program_id', 'reg_no', 'first_name', 'last_name', 'contact', 'email',
        'address', 'postcode', 'nationality', 'sin', 'dob', 'gender', 'enrollment_date'];

    public function user() : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function program() : BelongsTo {
        return $this->belongsTo(Program::class);
    }
}

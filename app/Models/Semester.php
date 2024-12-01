<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use SoftDeletes;

    protected $fillable = ['type', 'year', 'start_date', 'end_date', 'reg_begin_at', 'reg_lock_at'];
}

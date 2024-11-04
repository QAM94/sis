<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instructor extends Model
{
    use SoftDeletes;
    protected $fillable = ['full_name', 'gender', 'contact', 'address', 'nationality', 'cnic', 'dob',
        'joining_date', 'status'];
}

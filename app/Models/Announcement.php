<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['target', 'target_id', 'title', 'message', 'type', 'start_date', 'end_date',
        'is_active'];

}

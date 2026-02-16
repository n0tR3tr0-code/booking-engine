<?php

namespace App\Models;

use App\Traits\Lockable;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use Lockable;

    protected $fillable = [
        'user_id',
        'resource_id',
        'start_time',
        'end_time',
        'version',
    ];

}

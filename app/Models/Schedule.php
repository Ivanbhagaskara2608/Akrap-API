<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Schedule extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'activity_name',
        'date',
        'location',
        'start_time',
        'end_time',
        'attendance_code',
        'status'
    ];

    protected $casts = [
        'date' => 'date:d/m/Y',
        'start_time' => 'string:H:i',
        'end_time' => 'string:H:i'
    ];

    protected $primaryKey = "scheduleId";
}

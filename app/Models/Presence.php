<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Presence extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'scheduleId',
        'userId',
        'date',
        'status'
    ];

    protected $casts = [
        'date' => 'date:d/m/Y',
        "created_at" => "datetime:d-m-Y H:i:s"
    ];

    protected $primaryKey = "presenceId";

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->belongsTo(Schedule::class);
    }
}

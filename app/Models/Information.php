<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Information extends Model
{
    use HasFactory, HasApiTokens;
    protected $fillable = [
        'title',
        'content',
        'category',
        'image',
        'attachment',
        'read_by'
    ];

    protected $primaryKey = "informationId";
}

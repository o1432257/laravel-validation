<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marquee extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'display', 'forever', 'start_at', 'end_at'];
}

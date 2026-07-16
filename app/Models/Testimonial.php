<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $casts = [
        'rating' => 'integer',
        'status' => 'integer',
        'show_at_home' => 'integer',
    ];
}

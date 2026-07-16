<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chef extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => 'integer',
        'show_at_home' => 'integer',
    ];
}

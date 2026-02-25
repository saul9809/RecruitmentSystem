<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cv extends Model
{
    protected $fillable = ['cv_data'];

    protected $casts = [
        'cv_data' => 'array',
    ];
}

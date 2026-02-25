<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileRequirement extends Model
{
    protected $fillable = [
        'profile_id',
        'type',
        'key',
        'value',
        'weight',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}

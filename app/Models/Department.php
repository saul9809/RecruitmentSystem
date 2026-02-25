<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'description'];
    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'skill_name',
        'skill_description',
    ];
    public function candidates()
    {
        return $this->belongsToMany(Candidate::class, 'candidate_skills')
            ->withPivot(['level', 'origin'])
            ->withTimestamps();
    }
}

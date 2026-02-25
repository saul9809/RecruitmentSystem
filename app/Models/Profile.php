<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'name',
        'description',
        'department_id',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function requirements()
    {
        return $this->hasMany(ProfileRequirement::class);
    }
    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'candidate_profile_score')
            ->withPivot('score_percentage', 'details')
            ->withTimestamps();
    }
}

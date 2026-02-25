<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'cv',

    ];

    protected $casts = [
        'cv' => 'array',
    ];

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'candidate_skills')
            ->withPivot(['level', 'origin'])
            ->withTimestamps();
    }
    public function experiences()
    {
        return $this->hasMany(CandidateExperience::class);
    }
    public function educations()
    {
        return $this->hasMany(CandidateEducation::class);
    }
    public function stageHistory()
    {
        return $this->belongsToMany(CandidateStageHistory::class);
    }
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'candidate_profile_score')
            ->withPivot(['score_porcentage', 'details'])
            ->withTimestamps();
    }
}

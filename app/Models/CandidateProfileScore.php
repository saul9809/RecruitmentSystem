<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProfileScore extends Model
{
    protected $table = 'candidate_profile_scores';
    protected $casts = [
        'details' => 'array',
    ];
    protected $fillable = [
        'candidate_id',
        'profile_id',
        'score_porcentage',
        'details',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
}

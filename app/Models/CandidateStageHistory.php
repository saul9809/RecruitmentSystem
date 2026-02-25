<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateStageHistory extends Model
{
    protected $table = 'candidate_stage_histories';
    protected $fillable = [
        'candidate_id',
        'stage',
        'comments',
        'responsable_id',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function stage()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateEducation extends Model
{
    protected $fillable = [
        'candidate_id',
        'level',
        'center',
        'specialty',
        'year_end',
    ];
    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}

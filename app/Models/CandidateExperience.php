<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateExperience extends Model
{
    protected $fillable = [
        'company_name',
        'position',
        'years',
        'description',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}

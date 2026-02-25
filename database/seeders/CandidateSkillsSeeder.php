<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Skill;
use App\Models\CandidateSkill;
use Illuminate\Database\Seeder;

class CandidateSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $candidates = Candidate::all(); // ← TODOS los candidatos
        $skills     = Skill::all();

        if ($candidates->isEmpty()) {
            $this->command?->warn('No hay candidatos. Ejecuta primero CandidateSeeder.');
            return;
        }

        if ($skills->isEmpty()) {
            $this->command?->warn('No hay skills. Ejecuta primero SkillSeeder.');
            return;
        }

        foreach ($candidates as $candidate) {

            // Ejemplo: asignar entre 4 y 8 skills
            $assignedSkills = $skills->random(rand(4, 8));

            foreach ($assignedSkills as $skill) {
                CandidateSkill::updateOrCreate(
                    [
                        'candidate_id' => $candidate->id,
                        'skill_id'     => $skill->id,
                    ],
                    [
                        'level'  => collect(['básico', 'medio', 'avanzado'])->random(),
                        'origin' => 'cv',
                    ]
                );
            }
        }

        $this->command?->info('CandidateSkillSeeder: skills asignados a TODOS los candidatos.');
    }
}

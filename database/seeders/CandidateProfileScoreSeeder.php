<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CandidateProfileScore;
use App\Models\Candidate;
use App\Models\Profile;

class CandidateProfileScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * */
    public function run(): void
    {
        $candidates = Candidate::all();
        $profiles   = Profile::take(4)->get(); // ajusta si tienes más

        if ($candidates->isEmpty()) {
            $this->command?->warn('No hay candidatos. Ejecuta primero CandidateSeeder.');
            return;
        }
        if ($profiles->isEmpty()) {
            $this->command?->warn('No hay perfiles. Ejecuta primero ProfileSeeder.');
            return;
        }

        $total = 0;

        foreach ($candidates as $candidate) {
            foreach ($profiles as $profile) {
                // Heurística simple para un score “creíble” en demo, basado en el nombre del perfil
                $name = mb_strtolower($profile->name);
                $base = 55;
                if (str_contains($name, 'backend'))      $base = 72;
                elseif (str_contains($name, 'analista')) $base = 68;
                elseif (str_contains($name, 'soporte'))  $base = 60;
                elseif (str_contains($name, 'coord') || str_contains($name, 'proyecto')) $base = 65;

                $noise = random_int(-15, 20);
                $score = max(0, min(100, $base + $noise));

                $details = [
                    'weights' => [
                        'education' => 0.30,
                        'experience' => 0.50,
                        'skills'    => 0.20,
                    ],
                    'breakdown' => [
                        'education'  => random_int(50, 100),
                        'experience' => random_int(50, 100),
                        'skills'     => random_int(50, 100),
                    ],
                    'notes' => 'Datos de demo para pruebas de UI. Reemplazar por salida del AIRankingService.',
                    'model' => 'demo-static',
                ];

                CandidateProfileScore::updateOrCreate(
                    [
                        'candidate_id' => $candidate->id,
                        'profile_id'   => $profile->id,
                    ],
                    [
                        'score_percentage' => $score,
                        'details'          => $details,
                    ]
                );

                $total++;
            }
        }

        $this->command?->info("CandidateProfileScoreSeeder: generados/actualizados $total registros (candidato×perfil).");
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CandidateExperience;
use App\Models\Candidate;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CandidateExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $candidates = Candidate::all();

        if ($candidates->isEmpty()) {
            $this->command?->warn('No hay candidatos. Ejecuta primero CandidateSeeder.');
            return;
        }

        // Catálogos simples para variedad
        $companies = [
            'ETECSA',
            'Desoft',
            'Xetid',
            'CITMATEL',
            'GEOCUBA',
            'CENIA',
            'Cinesoft',
            'CUPET',
            'BioCubaFarma',
            'Correos de Cuba',
            'Banco Metropolitano',
        ];

        $positions = [
            'Desarrollador Backend',
            'Analista de Datos',
            'Soporte Técnico',
            'QA Tester',
            'Administrador de Sistemas',
            'Coordinador de Proyecto',
            'Desarrollador Fullstack',
            'DevOps',
        ];

        $total = 0;

        // (Opcional) Limpia experiencias anteriores si deseas un seed fresco
        // CandidateExperience::truncate();

        foreach ($candidates as $candidate) {
            $experiencesCount = random_int(1, 3);

            DB::transaction(function () use ($candidate, $experiencesCount, $companies, $positions, $faker, &$total) {
                for ($i = 0; $i < $experiencesCount; $i++) {
                    $company = $faker->randomElement($companies);
                    $position = $faker->randomElement($positions);
                    $years = $faker->numberBetween(1, 8);

                    CandidateExperience::create([
                        'candidate_id' => $candidate->id, // ← clave para asociar
                        'company_name' => $company,
                        'position'     => $position,
                        'years'        => $years,
                        'description'  => $faker->sentence(12),
                    ]);

                    $total++;
                }
            });
        }

        $this->command?->info("CandidateExperienceSeeder: creadas $total experiencias para {$candidates->count()} candidatos.");
    }
}

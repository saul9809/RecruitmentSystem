<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CandidateEducation;
use App\Models\Candidate;
use Faker\Factory as Faker;

class CandidateEducationSeeder extends Seeder
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

        // Catálogos realistas
        $levels = ['universitario', 'técnico', 'posgrado'];
        $centers = [
            'Universidad de La Habana',
            'CUJAE (Universidad Tecnológica de La Habana)',
            'Universidad de Matanzas',
            'Universidad de las Ciencias Informáticas (UCI)',
            'Universidad de Oriente',
            'Universidad de Camagüey',
            'Instituto Politécnico de Informática',
            'Instituto Politécnico Industrial',
        ];
        $specialties = [
            'Ingeniería Informática',
            'Ciencias de la Computación',
            'Telecomunicaciones',
            'Automática y Computación',
            'Redes y Seguridad',
            'Matemática',
            'Análisis de Datos',
            'Gestión de Proyectos',
            'Electrónica',
            'Software y Sistemas',
            'QA y Pruebas',
            'Business Intelligence',
        ];

        $total = 0;

        foreach ($candidates as $candidate) {
            // 1 o 2 estudios por candidato
            $eduCount = random_int(1, 2);

            for ($i = 0; $i < $eduCount; $i++) {
                // Distribución ponderada (más probabilidad de universitario)
                $level = $this->pickWeighted([
                    'universitario' => 65,
                    'técnico'       => 25,
                    'posgrado'      => 10,
                ]);

                // Año de fin plausible (últimos 3–15 años)
                $yearEnd = (int) date('Y') - random_int(3, 15);

                CandidateEducation::create([
                    'candidate_id' => $candidate->id,
                    'level'        => $level,
                    'center'       => $faker->randomElement($centers),
                    'specialty'    => $faker->randomElement($specialties),
                    'year_end'     => $yearEnd,
                ]);

                $total++;
            }
        }

        $this->command?->info("CandidateEducationSeeder: creadas $total formaciones para {$candidates->count()} candidatos.");
    }

    /**
     * Selección ponderada simple.
     */
    private function pickWeighted(array $weights): string
    {
        $sum = array_sum($weights);
        $rand = random_int(1, $sum);
        $acc = 0;
        foreach ($weights as $key => $w) {
            $acc += $w;
            if ($rand <= $acc) return $key;
        }
        return array_key_first($weights);
    }
}

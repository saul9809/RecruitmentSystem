<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Seeder;

class CandidatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        $usedPhones = [];
        $usedIds = [];

        for ($i = 1; $i <= 50; $i++) {
            $name = $faker->name();

            // Generar email y luego decidir si es null (~25%)
            $email = $faker->unique()->safeEmail();
            if ($faker->boolean(25)) {
                $email = null;
            }

            // Teléfono único
            do {
                $phoneNumber = '+53 5' . str_pad((string)random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
            } while (isset($usedPhones[$phoneNumber]));
            $usedPhones[$phoneNumber] = true;

            // CI único (11 dígitos)
            do {
                $ci = (string)random_int(10_000_000_000, 99_999_999_999);
            } while (isset($usedIds[$ci]));
            $usedIds[$ci] = true;

            $address = $faker->streetAddress() . ', ' . $faker->city();

            $cv = null;
            if ($faker->boolean(70)) {
                $cv = [
                    'nombre' => $name,
                    'cedula' => $ci,
                    'direccion' => $address,
                    'telefono' => $phoneNumber,
                    'correo' => $email,
                    'perfil_profesional' => $faker->paragraph(),
                    'experiencia_laboral' => [
                        [
                            'empresa' => 'Cervecería Cubana',
                            'cargo'   => $faker->randomElement([
                                'Especialista Informático',
                                'Desarrollador Backend',
                                'Analista de Datos',
                                'Soporte Técnico',
                            ]),
                            'desde'   => $faker->date('Y-m-d', '-6 years'),
                            'hasta'   => $faker->date('Y-m-d', '-1 years'),
                            'funciones' => $faker->sentences(3),
                        ],
                        [
                            'empresa' => $faker->company(),
                            'cargo'   => $faker->jobTitle(),
                            'desde'   => $faker->date('Y-m-d', '-10 years'),
                            'hasta'   => $faker->date('Y-m-d', '-6 years'),
                            'funciones' => $faker->sentences(3),
                        ],
                    ],
                    'educacion' => [
                        [
                            'centro' => $faker->randomElement([
                                'Universidad de La Habana',
                                'Universidad Tecnológica de La Habana (CUJAE)',
                                'Universidad de Matanzas',
                            ]),
                            'titulo' => $faker->randomElement([
                                'Ingeniería Informática',
                                'Licenciatura en Matemática',
                                'Ingeniería Automática',
                                'Técnico Superior en Informática',
                            ]),
                            'anno_graduacion' => (int) $faker->year(),
                        ],
                        [
                            'centro' => 'IPU Preuniversitario',
                            'titulo' => 'Bachiller',
                            'anno_graduacion' => (int) $faker->year(),
                        ],
                    ],
                    'habilidades' => $faker->randomElements(
                        ['Laravel', 'PHP', 'Vue.js', 'React', 'MySQL', 'PostgreSQL', 'Git', 'Linux', 'Docker', 'REST'],
                        random_int(4, 7)
                    ),
                    'idiomas' => [
                        ['idioma' => 'Español', 'nivel' => 'Nativo'],
                        ['idioma' => 'Inglés', 'nivel' => $faker->randomElement(['Básico', 'Intermedio'])],
                    ],
                ];
            }

            Candidate::create([
                'candidate_name'    => $name,
                'candidate_email'   => $email,
                'candidate_phone'   => $phoneNumber,
                'candidate_address' => $address,
                'candidate_id'      => $ci,
                'cv'                => $cv,
            ]);
        }
    }
}

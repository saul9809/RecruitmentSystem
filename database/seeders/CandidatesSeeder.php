<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Faker\Factory;
use Illuminate\Database\Seeder;

class CandidatesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('es_ES');

        $faker->unique(true); // reinicia control de unicidad

        for ($i = 1; $i <= 50; $i++) {

            //  Nombre totalmente único
            $name = $faker->unique()->name();

            //  Email SIEMPRE único
            $email = $faker->unique()->safeEmail();

            //  Teléfono totalmente único
            $phoneNumber = '+53 5'.str_pad($faker->unique()->numberBetween(0, 9999999), 7, '0', STR_PAD_LEFT);

            //  CI único
            $ci = (string) $faker->unique()->numberBetween(10_000_000_000, 99_999_999_999);

            //  Dirección única
            $address = $faker->unique()->streetAddress().', '.$faker->city();

            //  CV coherente
            $cv = [
                'nombre' => $name,
                'cedula' => $ci,
                'direccion' => $address,
                'telefono' => $phoneNumber,
                'correo' => $email,
                'perfil_profesional' => $faker->paragraph(),
                'experiencia_laboral' => [
                    [
                        'empresa' => $faker->company(),
                        'cargo' => $faker->jobTitle(),
                        'desde' => $faker->date('Y-m-d', '-6 years'),
                        'hasta' => $faker->date('Y-m-d', '-1 years'),
                        'funciones' => $faker->sentences(3),
                    ],
                ],
                'educacion' => [
                    [
                        'centro' => $faker->unique()->company(),
                        'titulo' => $faker->randomElement([
                            'Ingeniería Informática',
                            'Licenciatura en Matemática',
                            'Ingeniería Automática',
                        ]),
                        'anno_graduacion' => (int) $faker->year(),
                    ],
                ],
                'habilidades' => $faker->randomElements(
                    ['Laravel', 'PHP', 'React', 'PostgreSQL', 'Git', 'Linux', 'Docker'],
                    random_int(3, 6)
                ),
                'idiomas' => [
                    ['idioma' => 'Español', 'nivel' => 'Nativo'],
                    ['idioma' => 'Inglés', 'nivel' => $faker->randomElement(['Básico', 'Intermedio', 'Avanzado'])],
                ],
            ];

            Candidate::create([
                'candidate_name' => $name,
                'candidate_email' => $email,
                'candidate_phone' => $phoneNumber,
                'candidate_address' => $address,
                'candidate_id' => $ci,
                'cv' => $cv,
            ]);
        }
    }
}

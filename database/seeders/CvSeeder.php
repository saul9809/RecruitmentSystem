<?php

namespace Database\Seeders;

use App\Models\Cv;
use Illuminate\Database\Seeder;

class CvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        for ($i = 1; $i <= 50; $i++) {
            Cv::create([
                'cv_data' => [
                    'nombre' => $faker->name(),
                    'cedula' => str_pad(random_int(80000000000, 99999999999), 11, '0', STR_PAD_LEFT),
                    'direccion' => $faker->address(),
                    'telefono' => '+53 ' . $faker->numberBetween(52000000, 59999999),
                    'correo' => $faker->unique()->safeEmail(),
                    'perfil_profesional' => $faker->paragraph(),

                    'experiencia_laboral' => [
                        [
                            'empresa' => 'Cervecería Cubana',
                            'cargo' => 'Especialista Informático',
                            'desde' => $faker->date('Y-m-d', '-5 years'),
                            'hasta' => $faker->date('Y-m-d', '-1 years'),
                            'funciones' => $faker->sentences(3),
                        ],
                        [
                            'empresa' => $faker->company(),
                            'cargo' => $faker->jobTitle(),
                            'desde' => $faker->date('Y-m-d', '-10 years'),
                            'hasta' => $faker->date('Y-m-d', '-5 years'),
                            'funciones' => $faker->sentences(3),
                        ],
                    ],

                    'educacion' => [
                        [
                            'centro' => 'Universidad de La Habana',
                            'titulo' => 'Ingeniería Informática',
                            'anno_graduacion' => $faker->year(),
                        ],
                        [
                            'centro' => 'IPU Preuniversitario',
                            'titulo' => 'Bachiller',
                            'anno_graduacion' => $faker->year(),
                        ],
                    ],

                    'habilidades' => [
                        'Laravel',
                        'PHP',
                        'Vue.js',
                        'MySQL',
                        'Git',
                        'Linux'
                    ],

                    'idiomas' => [
                        ['idioma' => 'Español', 'nivel' => 'Nativo'],
                        ['idioma' => 'Inglés', 'nivel' => $faker->randomElement(['Básico', 'Intermedio'])],
                    ],
                ],
            ]);
        }
    }
}

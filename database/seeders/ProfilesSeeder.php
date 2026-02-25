<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Department;
use Illuminate\Database\Seeder;

class ProfilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        // Obtener IDs de departamentos existentes
        $departmentIds = Department::query()->pluck('id')->all();

        // Si no hay departamentos, crear algunos por seguridad
        if (empty($departmentIds)) {
            $fallback = [
                ['department_name' => 'Information Technology', 'department_description' => 'IT Operations', 'location' => 'Building E'],
                ['department_name' => 'Human Resources',        'department_description' => 'HR & People',   'location' => 'Building A'],
                ['department_name' => 'Finance',                 'department_description' => 'Accounting',    'location' => 'Building B'],
                ['department_name' => 'Marketing',               'department_description' => 'Brand & Growth', 'location' => 'Building C'],
            ];
            foreach ($fallback as $dep) {
                $departmentIds[] = Department::create($dep)->id;
            }
        }

        $profiles = [
            [
                'name'       => 'Desarrollador Backend',
                'experience' => '5+ años desarrollando APIs con Laravel, PostgreSQL y patrones de arquitectura limpia.',
                'age'        => 32.0,
                'skills'     => 'Laravel, PHP, PostgreSQL, Docker, Git',
            ],
            [
                'name'       => 'Analista de Datos',
                'experience' => 'Modelado de datos, ETL, dashboards y reporting para áreas de negocio.',
                'age'        => 29.0,
                'skills'     => 'SQL, Análisis de datos, Excel, Comunicación',
            ],
            [
                'name'       => 'Especialista de Soporte TI',
                'experience' => 'Atención a usuarios, gestión de incidencias, administración básica de Linux y redes.',
                'age'        => 27.5,
                'skills'     => 'Linux, Redes, Atención al cliente, Resolución de problemas',
            ],
            [
                'name'       => 'Coordinador de Proyectos',
                'experience' => 'Planificación, seguimiento y coordinación con stakeholders bajo marcos ágiles.',
                'age'        => 35.0,
                'skills'     => 'Liderazgo, Scrum, Comunicación, Gestión del tiempo',
            ],
        ];

        foreach ($profiles as $p) {
            Profile::create([
                'name'          => $p['name'],
                'experience'    => $p['experience'],
                'department_id' => $faker->randomElement($departmentIds),
                'age'           => $p['age'],
                'skills'        => $p['skills'],
            ]);
        }
    }
}

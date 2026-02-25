<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $faker = \Faker\Factory::create('es_ES');

        $types = ['Liderazgo', 'Blandas', 'Técnicas'];
        $criticality = ['Baja', 'Media', 'Alta', 'Crítica'];

        $skills = [
            'Comunicación efectiva',
            'Trabajo en equipo',
            'Resolución de problemas',
            'Gestión del tiempo',
            'Liderazgo estratégico',
            'Toma de decisiones',
            'Programación en PHP',
            'Administración de bases de datos',
            'Análisis de datos',
            'Atención al cliente',
        ];

        foreach ($skills as $skillName) {
            Skill::create([
                'skill_name'        => $skillName,
                'skill_description' => $faker->sentence(8),
                'skill_type'        => $faker->randomElement($types),
                'criticality_level' => $faker->randomElement($criticality),
            ]);
        }
    }
}

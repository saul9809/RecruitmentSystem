<?php

namespace Database\Seeders;

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use App\Models\ProfileRequirement;
use App\Models\Profile;

class ProfileRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        // Traer hasta 4 perfiles (asume que ya sembraste ProfileSeeder)
        $profiles = Profile::query()->take(4)->get();

        if ($profiles->isEmpty()) {
            $this->command?->warn('No hay perfiles en la tabla profiles. Ejecuta primero ProfileSeeder.');
            return;
        }

        // Catálogos base
        $skillsBackend = [
            ['key' => 'Laravel',     'value' => 'avanzado',   'weight' => 20],
            ['key' => 'PHP',         'value' => 'avanzado',   'weight' => 10],
            ['key' => 'PostgreSQL',  'value' => 'intermedio', 'weight' => 8],
            ['key' => 'Docker',      'value' => 'intermedio', 'weight' => 6],
            ['key' => 'Git',         'value' => 'intermedio', 'weight' => 6],
        ];
        $expBackend = [
            ['key' => 'años_experiencia', 'value' => '3', 'weight' => 15],
        ];
        $eduBackend = [
            ['key' => 'titulo', 'value' => 'Ingeniería Informática', 'weight' => 10],
        ];
        $kwBackend = [
            ['key' => 'microservicios',      'value' => null, 'weight' => 5],
            ['key' => 'arquitectura limpia', 'value' => null, 'weight' => 5],
        ];

        $skillsData = [
            'backend'    => $skillsBackend,
            'analista'   => [
                ['key' => 'SQL',               'value' => 'avanzado',   'weight' => 18],
                ['key' => 'Análisis de datos', 'value' => 'avanzado',   'weight' => 12],
                ['key' => 'Excel',             'value' => 'avanzado',   'weight' => 8],
                ['key' => 'Comunicación',      'value' => 'intermedio', 'weight' => 6],
                ['key' => 'Power BI',          'value' => 'intermedio', 'weight' => 6],
            ],
            'soporte'    => [
                ['key' => 'Linux',                  'value' => 'intermedio', 'weight' => 12],
                ['key' => 'Redes',                  'value' => 'intermedio', 'weight' => 10],
                ['key' => 'Atención al cliente',    'value' => 'avanzado',   'weight' => 10],
                ['key' => 'Resolución de problemas', 'value' => 'intermedio', 'weight' => 8],
                ['key' => 'Documentación',          'value' => 'intermedio', 'weight' => 6],
            ],
            'coordinador' => [
                ['key' => 'Liderazgo',         'value' => 'avanzado',   'weight' => 15],
                ['key' => 'Scrum',             'value' => 'intermedio', 'weight' => 10],
                ['key' => 'Comunicación',      'value' => 'avanzado',   'weight' => 10],
                ['key' => 'Gestión del tiempo', 'value' => 'intermedio', 'weight' => 8],
                ['key' => 'Planificación',     'value' => 'intermedio', 'weight' => 7],
            ],
        ];

        $expData = [
            'backend'     => $expBackend,
            'analista'    => [['key' => 'años_experiencia', 'value' => '2', 'weight' => 12]],
            'soporte'     => [['key' => 'años_experiencia', 'value' => '1', 'weight' => 10]],
            'coordinador' => [['key' => 'años_experiencia', 'value' => '3', 'weight' => 12]],
        ];

        $eduData = [
            'backend'     => $eduBackend,
            'analista'    => [['key' => 'titulo', 'value' => 'Licenciatura en Matemática',      'weight' => 10]],
            'soporte'     => [['key' => 'titulo', 'value' => 'Técnico Superior en Informática', 'weight' => 8]],
            'coordinador' => [['key' => 'titulo', 'value' => 'Ingeniería Industrial',           'weight' => 8]],
        ];

        $kwData = [
            'backend'     => $kwBackend,
            'analista'    => [
                ['key' => 'ETL',        'value' => null, 'weight' => 5],
                ['key' => 'dashboards', 'value' => null, 'weight' => 5],
            ],
            'soporte'     => [
                ['key' => 'tickets',    'value' => null, 'weight' => 4],
                ['key' => 'SLA',        'value' => null, 'weight' => 4],
            ],
            'coordinador' => [
                ['key' => 'stakeholders', 'value' => null, 'weight' => 5],
                ['key' => 'indicadores', 'value' => null, 'weight' => 5],
            ],
        ];

        // Mapeo de perfiles por nombre esperado (ajusta a tu data real)
        $mapByName = function (Profile $p): string {
            $name = mb_strtolower($p->name);
            if (str_contains($name, 'backend')) return 'backend';
            if (str_contains($name, 'analista')) return 'analista';
            if (str_contains($name, 'soporte'))  return 'soporte';
            if (str_contains($name, 'coordinador') || str_contains($name, 'proyecto')) return 'coordinador';
            return 'backend';
        };

        foreach ($profiles as $profile) {
            $kind = $mapByName($profile);

            $reqs = [];

            // skills
            foreach (Arr::get($skillsData, $kind, []) as $it) {
                $reqs[] = ['type' => 'skill'] + $it;
            }
            // experiencia
            foreach (Arr::get($expData, $kind, []) as $it) {
                $reqs[] = ['type' => 'experiencia'] + $it;
            }
            // educación
            foreach (Arr::get($eduData, $kind, []) as $it) {
                $reqs[] = ['type' => 'educacion'] + $it;
            }
            // palabras clave (si tu modelo soporta este type; si no, comenta esta parte)
            foreach (Arr::get($kwData, $kind, []) as $it) {
                $reqs[] = ['type' => 'keyword'] + $it;
            }

            // Opcional: evitar duplicados por perfil+type+key
            foreach ($reqs as $r) {
                ProfileRequirement::updateOrCreate(
                    [
                        'profile_id' => $profile->id,
                        'type'       => $r['type'],
                        'key'        => $r['key'] ?? null,
                    ],
                    [
                        'value'      => $r['value'] ?? null,
                        'weight'     => $r['weight'] ?? 0,
                    ],
                );
            }
        }

        $this->command?->info('ProfileRequirementSeeder: requisitos generados/actualizados.');
    }
}

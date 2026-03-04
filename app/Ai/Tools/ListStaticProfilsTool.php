<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use App\Models\Profile;
use Stringable;

class ListStaticProfilsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Lista perfiles filtrando por nombre, experiencia, departamento, 
        edad y habilidades. Soporta búsqueda general, coincidencia (contiene, 
        comienza, termina, exacta), combinación (y/o), ordenación y paginación. 
        Optimizado para PostgreSQL.';
    }
    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $value = $request['profiles']
            ?? $request['name']
            ?? $request['experience']
            ?? $request['department']
            ?? $request['skills']
            ?? null;

        $profiles = Profile::query()
            ->when($value, function ($query) use ($value) {
                $query->where(function ($q) use ($value) {
                    $q->where('name', 'like', "%{$value}%")
                        ->orWhere('experience', 'like', "%{$value}%")
                        ->orWhere('skills', 'like', "%{$value}%")
                        // Coincidencia por nombre del departamento (relación)
                        ->orWhereHas('department', function ($dq) use ($value) {
                            $dq->where('department_name', 'like', "%{$value}%");
                        });
                });
            })->get();
        return (string) $profiles;
    }
    public function schema(JsonSchema $schema): array
    {
        return [
            'profiles' => $schema->string()->nullable()->description('El nombre, experiencia, departamento o habilidades del perfil a buscar'),
        ];
    }
}

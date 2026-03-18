<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Stringable;
//PENMDIENTE A TERMINAR CONSULTA Y EL SCHEMA DE RETORNO 
class ListRequireProfilsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Obtener los perfiles requeridos para un proceso de selección específico, 
        filtrando por nombre del proceso, departamento, habilidades o experiencia requerida. 
        Devuelve una lista de perfiles con sus detalles relevantes.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        /** @var Builder $q */

        $q = Profile::query()->select(
            'id',
            'name ',
            'experience',
            'departmen t_id ',
            'age',
            'skills',
            'created_at'
        )->orderByDesc('created_at');
        return 'Pendiente';
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'profile' => $schema->string()->required(),
        ];
    }
}

<?php

namespace App\Ai\Tools;

use App\Models\Candidate;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;


class FilterCandiadateByDataTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Listame todos los candidatos o filtra por nombre, email, correo o telefono.';
    }

    /**
     * Execute the tool.
     */

    public function handle(Request $request): Stringable|string
    {
        $value = $request['candidate'] ?? $request['name'] ?? $request['email'] ?? $request['phone'] ?? null;

        $candidates = Candidate::query()
            ->when($value, function ($query) use ($value) {
                $query->where(function ($q) use ($value) {
                    $q->where('candidate_name', 'like', "%{$value}%")
                        ->orWhere('candidate_email', 'like', "%{$value}%")
                        ->orWhere('candidate_phone', 'like', "%{$value}%");
                });
            })
            ->get();

        return $candidates;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'candidate' => $schema->string()->nullable()->description('Nombre, email o teléfono del candidato a buscar'),
            'name'      => $schema->string()->nullable()->description('Nombre del candidato (LIKE)'),
            'email'     => $schema->string()->nullable()->description('Correo del candidato (LIKE)'),
            'phone'     => $schema->string()->nullable()->description('Teléfono del candidato (LIKE)'),
        ];
    }
}

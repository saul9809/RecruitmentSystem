<?php

namespace App\Ai\Tools;

use App\Models\Candidate;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Illuminate\Database\Eloquent\Builder;
use Stringable;

class FilterCandiadateByDataTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Lista o cuenta candidatos filtrando por experiencia, educación, especialidad (skills), dirección, estado y rango de edad. Soporta limit y agrupación básica.';
    }

    public function handle(Request $request): Stringable|string
    {
        // Filtros base
        $experience = trim((string) ($request['experience'] ?? ''));
        $education  = trim((string) ($request['education']  ?? ''));
        $specialty  = trim((string) ($request['specialty']  ?? '')); // busca en skills
        $address    = trim((string) ($request['address']    ?? ''));
        $status     = trim((string) ($request['status']     ?? ''));
        $limit      = (int) ($request['limit'] ?? 50);

        // Extensiones
        $mode     = $request['mode'] ?? 'list'; // 'list' | 'count' | 'both'
        $groupBy  = $request['group_by'] ?? null; // 'education' | 'experience' | 'address' | 'status' | 'specialty'

        // Saneos
        $mode = in_array($mode, ['list', 'count', 'both'], true) ? $mode : 'list';
        if ($limit <= 0) $limit = 50;
        if ($limit > 200) $limit = 200;

        /** @var Builder $q */
        $q = Candidate::query()
            ->select([
                'id',
                'candidate_name',
                'candidate_email',
                'candidate_phone',
                'address',
                'status',
                'skills',
                'experience',
                'education',
                'birth_date',
                'created_at'
            ])
            ->orderByDesc('created_at');

        // AND filters
        if ($status !== '') {
            $q->where('status', $status);
        }
        if ($experience !== '') {
            $q->where('experience', 'like', '%' . $this->escapeLike($experience) . '%');
        }
        if ($education !== '') {
            $q->where('education', 'like', '%' . $this->escapeLike($education) . '%');
        }
        if ($specialty !== '') {
            $q->where('skills', 'like', '%' . $this->escapeLike($specialty) . '%');
        }
        if ($address !== '') {
            $q->where('address', 'like', '%' . $this->escapeLike($address) . '%');
        }

        // -- Pendiente a implementar edad --

        // Conteo/agrupación
        if ($mode === 'count' || $mode === 'both') {
            $base  = (clone $q);
            $total = (clone $base)->count();

            $groups = null;
            if ($groupBy) {
                $allowed = ['education', 'experience', 'address', 'status'];
                if ($groupBy === 'specialty') {
                    // Nota: si specialty viene filtrado, devolvemos ese bucket.
                    if ($specialty !== '') {
                        $groups = [
                            ['key' => $specialty, 'total' => $total]
                        ];
                    }
                } elseif (in_array($groupBy, $allowed, true)) {
                    $groups = (clone $base)
                        ->selectRaw($groupBy . ' as `key`, COUNT(*) as total')
                        ->groupBy($groupBy)
                        ->orderByDesc('total')
                        ->limit(100)
                        ->get()
                        ->map(fn($r) => ['key' => (string)($r->key ?? ''), 'total' => (int)$r->total])
                        ->all();
                }
            }

            if ($mode === 'count') {
                return $this->json([
                    'total'  => $total,
                    'groups' => $groups,
                ]);
            }

            // both -> también listamos
            $items = $q->limit($limit)->get()->map(fn($c) => $this->serializeCandidate($c))->all();

            return $this->json([
                'total'  => $total,
                'groups' => $groups,
                'items'  => $items,
            ]);
        }

        // list por defecto
        $items = $q->limit($limit)->get()->map(fn($c) => $this->serializeCandidate($c))->all();
        return $this->json($items);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            // Filtros
            'experience' => $schema->string()->nullable()->description('Texto en experiencia (LIKE)'),
            'education'  => $schema->string()->nullable()->description('Texto en educación (LIKE)'),
            'specialty'  => $schema->string()->nullable()->description('Especialidad/skill (LIKE en skills)'),
            'address'    => $schema->string()->nullable()->description('Dirección/ubicación (LIKE)'),
            'status'     => $schema->string()->nullable()->description('Estado del proceso (ej. entrevistado, aceptado, en_proceso)'),
            'age_min'    => $schema->integer()->nullable()->description('Edad mínima (inclusive)'),
            'age_max'    => $schema->integer()->nullable()->description('Edad máxima (inclusive)'),

            // Opciones
            'limit'      => $schema->integer()->nullable()->description('Máximo de resultados (1..200, default 50)'),
            'mode'       => $schema->string()->nullable()->description("Modo: 'list' | 'count' | 'both' (default 'list')"),
            'group_by'   => $schema->string()->nullable()->description("Agrupar conteos por: 'education' | 'experience' | 'address' | 'status' | 'specialty'"),
        ];
    }

    protected function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
    }

    protected function json($payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function serializeCandidate($c): array
    {
        return [
            'id'              => $c->id,
            'name'            => $c->candidate_name,
            'email'           => $c->candidate_email,
            'phone'           => $c->candidate_phone,
            'address'         => $c->address,
            'status'          => $c->status,
            'skills'          => $c->skills,
            'experience'      => $c->experience,
            'education'       => $c->education,
            'birth_date'      => $c->birth_date,
            'created_at'      => optional($c->created_at)->toDateTimeString(),
        ];
    }
}

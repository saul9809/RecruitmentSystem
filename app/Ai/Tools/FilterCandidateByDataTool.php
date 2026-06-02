<?php

namespace App\Ai\Tools;

use App\Models\Candidate;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class FilterCandidateByDataTool implements Tool  // ← Corregido el typo
{
    public function description(): Stringable|string
    {
        return 'Lista o cuenta candidatos filtrando por experiencia, educación, especialidad (skills), 
        dirección, estado y rango de edad o nombre. Soporta limit y agrupación básica.';
    }

    public function handle(Request $request): Stringable|string
    {
        // Filtros base
        $experience = trim((string) ($request['experience'] ?? ''));
        $education = trim((string) ($request['education'] ?? ''));
        $specialty = trim((string) ($request['specialty'] ?? ''));
        $address = trim((string) ($request['address'] ?? ''));
        $status = trim((string) ($request['status'] ?? ''));
        $name = trim((string) ($request['name'] ?? '')); // ← Agregado filtro por nombre
        $limit = (int) ($request['limit'] ?? 50);

        // Filtros de edad
        $ageMin = isset($request['age_min']) ? (int) $request['age_min'] : null;
        $ageMax = isset($request['age_max']) ? (int) $request['age_max'] : null;

        // Extensiones
        $mode = $request['mode'] ?? 'list';
        $groupBy = $request['group_by'] ?? null;

        // Saneos
        $mode = in_array($mode, ['list', 'count', 'both'], true) ? $mode : 'list';
        if ($limit <= 0) {
            $limit = 50;
        }
        if ($limit > 200) {
            $limit = 200;
        }

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
                'created_at',
            ])
            ->orderByDesc('created_at');

        // AND filters con validación mejorada
        if ($status !== '') {
            $q->where('status', $status);
        }

        if ($experience !== '') {
            $q->where('experience', 'like', '%'.$this->escapeLike($experience).'%');
        }

        if ($education !== '') {
            $q->where('education', 'like', '%'.$this->escapeLike($education).'%');
        }

        if ($specialty !== '') {
            // Mejorado para manejar campos JSON
            $q->where(function ($query) use ($specialty) {
                $query->where('skills', 'like', '%'.$this->escapeLike($specialty).'%')
                    ->orWhere('skills', 'like', '%"'.$this->escapeLike($specialty).'"%'); // Para JSON
            });
        }

        if ($address !== '') {
            $q->where('address', 'like', '%'.$this->escapeLike($address).'%');
        }

        if ($name !== '') {
            $q->where('candidate_name', 'like', '%'.$this->escapeLike($name).'%');
        }

        // Filtro de edad implementado
        if ($ageMin !== null && $ageMin > 0) {
            $q->whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= ?', [$ageMin]);
        }

        if ($ageMax !== null && $ageMax > 0) {
            $q->whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= ?', [$ageMax]);
        }

        // Conteo/agrupación
        if ($mode === 'count' || $mode === 'both') {
            $base = (clone $q);
            $total = (clone $base)->count();

            $groups = null;
            if ($groupBy) {
                $allowed = ['education', 'experience', 'address', 'status'];

                if ($groupBy === 'specialty') {
                    if ($specialty !== '') {
                        $groups = [
                            ['key' => $specialty, 'total' => $total],
                        ];
                    } else {
                        // Si no hay filtro de specialty, agrupar por skills es complejo
                        // Podríamos omitir o devolver un mensaje
                        $groups = [
                            ['key' => 'agrupación_no_disponible', 'total' => $total],
                        ];
                    }
                } elseif (in_array($groupBy, $allowed, true)) {
                    try {
                        $groups = (clone $base)
                            ->selectRaw($groupBy.' as `key`, COUNT(*) as total')
                            ->groupBy($groupBy)
                            ->orderByDesc('total')
                            ->limit(100)
                            ->get()
                            ->map(fn ($r) => [
                                'key' => (string) ($r->key ?? 'sin_dato'),
                                'total' => (int) $r->total,
                            ])
                            ->all();
                    } catch (\Exception $e) {
                        // Si falla el groupBy (ej: campo muy largo), devolver error controlado
                        $groups = [
                            ['key' => 'error_agrupacion', 'total' => $total],
                        ];
                    }
                }
            }

            if ($mode === 'count') {
                return $this->json([
                    'total' => $total,
                    'groups' => $groups,
                ]);
            }

            // both -> también listamos
            $items = $q->limit($limit)
                ->get()
                ->map(fn ($c) => $this->serializeCandidate($c))
                ->all();

            return $this->json([
                'total' => $total,
                'groups' => $groups,
                'items' => $items,
            ]);
        }

        // list por defecto
        $items = $q->limit($limit)
            ->get()
            ->map(fn ($c) => $this->serializeCandidate($c))
            ->all();

        return $this->json($items);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            // Filtros
            'experience' => $schema->string()->nullable()->description('Texto en experiencia (LIKE)'),
            'education' => $schema->string()->nullable()->description('Texto en educación (LIKE)'),
            'specialty' => $schema->string()->nullable()->description('Especialidad/skill (LIKE en skills)'),
            'address' => $schema->string()->nullable()->description('Dirección/ubicación (LIKE)'),
            'status' => $schema->string()->nullable()->description('Estado del proceso (ej. entrevistado, aceptado, en_proceso)'),
            'name' => $schema->string()->nullable()->description('Nombre del candidato (LIKE)'), // ← Agregado
            'age_min' => $schema->integer()->nullable()->description('Edad mínima (inclusive)'),
            'age_max' => $schema->integer()->nullable()->description('Edad máxima (inclusive)'),

            // Opciones
            'limit' => $schema->integer()->nullable()->description('Máximo de resultados (1..200, default 50)'),
            'mode' => $schema->string()->nullable()->description("Modo: 'list' | 'count' | 'both' (default 'list')"),
            'group_by' => $schema->string()->nullable()->description("Agrupar conteos por: 'education' | 'experience' | 'address' | 'status' | 'specialty'"),
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
            'id' => $c->id,
            'name' => $c->candidate_name,
            'email' => $c->candidate_email,
            'phone' => $c->candidate_phone,
            'address' => $c->address,
            'status' => $c->status,
            'skills' => $c->skills,
            'experience' => $c->experience,
            'education' => $c->education,
            'birth_date' => $c->birth_date,
            'age' => $this->calculateAge($c->birth_date), // ← Agregado: calcular edad
            'created_at' => optional($c->created_at)->toDateTimeString(),
        ];
    }

    // Método auxiliar para calcular edad
    protected function calculateAge(?string $birthDate): ?int
    {
        if (empty($birthDate)) {
            return null;
        }

        try {
            $birth = new \DateTime($birthDate);
            $today = new \DateTime;

            return $birth->diff($today)->y;
        } catch (\Exception $e) {
            return null;
        }
    }
}

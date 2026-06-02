<?php

namespace App\Ai\Tools;

use App\Models\Candidate;
use App\Models\Profile;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class CandidateRankingTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Analiza y lista candidatos con mayor compatibilidad con el perfil requerido.
        Calcula un score de compatibilidad basado en la experiencia, educación, especialidad (skills),
        dirección, estado y rango de edad en comparación con los perfiles. 
        Soporta limit y agrupación básica.';
    }

    public function handle(Request $request): Stringable|string
    {
        $profileId = (int) ($request['profile_id'] ?? 0);
        $limit = (int) ($request['limit'] ?? 10);
        $minScore = (float) ($request['min_score'] ?? 0);
        $groupBy = $request['group_by'] ?? null;

        // Validaciones básicas
        if ($profileId <= 0) {
            return $this->json([
                'error' => 'Se requiere un profile_id válido',
                'rankings' => [],
            ]);
        }

        $profile = Profile::select('id', 'name', 'experience', 'age', 'skills', 'department_id')
            ->find($profileId);

        if (! $profile) {
            return $this->json([
                'error' => "Perfil con ID {$profileId} no encontrado",
                'rankings' => [],
            ]);
        }

        // Sanear límite
        if ($limit <= 0) {
            $limit = 10;
        }
        if ($limit > 50) {
            $limit = 50;
        }

        // Obtener candidatos
        $candidates = Candidate::select([
            'id', 'candidate_name', 'candidate_email', 'candidate_phone',
            'address', 'status', 'skills', 'experience', 'education',
            'birth_date', 'created_at',
        ])->get();

        if ($candidates->isEmpty()) {
            return $this->json([
                'profile' => $this->serializeProfile($profile),
                'rankings' => [],
                'total_analyzed' => 0,
            ]);
        }

        // Calcular rankings
        $rankings = [];
        foreach ($candidates as $candidate) {
            $score = $this->calculateScore($candidate, $profile);
            if ($score['total'] >= $minScore) {
                $rankings[] = [
                    'candidate' => $this->serializeCandidate($candidate),
                    'compatibility' => $score,
                ];
            }
        }

        // Ordenar por score descendente
        usort($rankings, fn ($a, $b) => $b['compatibility']['total'] <=> $a['compatibility']['total']);

        // Limitar resultados
        $rankings = array_slice($rankings, 0, $limit);

        // Agrupación si se solicita
        $grouped = null;
        if ($groupBy && in_array($groupBy, ['status', 'education', 'experience'])) {
            $grouped = [];
            foreach ($rankings as $ranking) {
                $key = match ($groupBy) {
                    'status' => $ranking['candidate']['status'] ?? 'sin_estado',
                    'education' => $this->extractEducationLevel($ranking['candidate']['education']),
                    'experience' => $this->extractExperienceLevel($ranking['candidate']['experience']),
                    default => 'otros'
                };
                $grouped[$key][] = $ranking;
            }
        }

        return $this->json([
            'profile' => $this->serializeProfile($profile),
            'rankings' => $rankings,
            'grouped_by' => $grouped,
            'total_analyzed' => $candidates->count(),
            'matching_candidates' => count($rankings),
        ]);
    }

    private function calculateScore($candidate, $profile): array
    {
        // 1. Skills (35%)
        $skillsScore = $this->compareSkills($candidate->skills, $profile->skills);

        // 2. Experiencia (30%)
        $expScore = $this->compareExperience($candidate->experience, $profile->experience);

        // 3. Educación (15%)
        $eduScore = $this->evaluateEducation($candidate->education);

        // 4. Ubicación (10%)
        $locationScore = $this->evaluateLocation($candidate->address, $candidate->status);

        // 5. Edad (10%)
        $ageScore = $this->evaluateAge($candidate->birth_date, $profile->age);

        // Ponderación
        $total = round(
            ($skillsScore * 0.35) +
            ($expScore * 0.30) +
            ($eduScore * 0.15) +
            ($locationScore * 0.10) +
            ($ageScore * 0.10),
            2);

        return [
            'total' => $total,
            'breakdown' => [
                'skills' => $skillsScore,
                'experience' => $expScore,
                'education' => $eduScore,
                'location' => $locationScore,
                'age' => $ageScore,
            ],
            'summary' => $this->getScoreSummary($total),
        ];
    }

    private function compareSkills(?string $candidateSkills, ?string $requiredSkills): float
    {
        if (empty($requiredSkills)) {
            return 100.0;
        }
        if (empty($candidateSkills)) {
            return 0.0;
        }

        $candidate = $this->parseSkills($candidateSkills);
        $required = $this->parseSkills($requiredSkills);

        if (empty($required)) {
            return 100.0;
        }

        $matches = 0;
        foreach ($required as $reqSkill) {
            $req = mb_strtolower(trim($reqSkill));
            foreach ($candidate as $candSkill) {
                $cand = mb_strtolower(trim($candSkill));
                if ($req === $cand || str_contains($cand, $req) || str_contains($req, $cand)) {
                    $matches++;
                    break;
                }
            }
        }

        return round(($matches / count($required)) * 100, 2);
    }

    private function compareExperience(?string $candidateExp, ?string $requiredExp): float
    {
        if (empty($requiredExp)) {
            return 100.0;
        }
        if (empty($candidateExp)) {
            return 0.0;
        }

        $candYears = $this->extractYears($candidateExp);
        $reqYears = $this->extractYears($requiredExp);

        if ($reqYears == 0) {
            return 100.0;
        }
        if ($candYears >= $reqYears) {
            return 100.0;
        }

        return round(($candYears / $reqYears) * 100, 2);
    }

    private function evaluateEducation(?string $education): float
    {
        if (empty($education)) {
            return 10.0;
        }

        $education = mb_strtolower($education);
        $levels = [
            'doctorado' => 100, 'maestría' => 90, 'maestria' => 90,
            'ingeniería' => 80, 'ingenieria' => 80, 'licenciatura' => 75,
            'universitario' => 60, 'tecnólogo' => 50, 'tecnologo' => 50,
            'técnico' => 40, 'tecnico' => 40, 'bachillerato' => 30,
            'secundaria' => 20,
        ];

        foreach ($levels as $keyword => $score) {
            if (str_contains($education, $keyword)) {
                return (float) $score;
            }
        }

        return 25.0; // Nivel no identificado
    }

    private function evaluateLocation(?string $address, ?string $status): float
    {
        if (empty($address)) {
            return 20.0;
        }

        $status = mb_strtolower($status ?? '');
        if (str_contains($status, 'remoto') || str_contains($status, 'reubicación')) {
            return 90.0;
        }

        return 80.0; // Tiene ubicación definida
    }

    private function evaluateAge(?string $birthDate, ?string $requiredAge): float
    {
        if (empty($birthDate) || empty($requiredAge)) {
            return 100.0;
        }

        $age = $this->calculateAge($birthDate);
        if (! $age) {
            return 100.0;
        }

        // Extraer rango de edad requerido
        $range = $this->parseAgeRange($requiredAge);
        if (! $range) {
            return 100.0;
        }

        if ($age >= $range['min'] && $age <= $range['max']) {
            return 100.0;
        }
        if ($age < $range['min']) {
            return max(0, 100 - (($range['min'] - $age) * 20));
        }

        return max(0, 100 - (($age - $range['max']) * 20));
    }

    // Métodos auxiliares
    private function parseSkills(?string $skills): array
    {
        if (empty($skills)) {
            return [];
        }

        $decoded = json_decode($skills, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return array_map('trim', explode(',', $skills));
    }

    private function extractYears(string $text): float
    {
        if (preg_match('/(\d+\.?\d*)\s*años?/i', $text, $matches)) {
            return (float) $matches[1];
        }
        if (preg_match('/(\d+)/', $text, $matches)) {
            return (float) $matches[1];
        }

        return 0;
    }

    private function calculateAge(?string $birthDate): ?int
    {
        if (empty($birthDate)) {
            return null;
        }
        try {
            $birth = new \DateTime($birthDate);

            return $birth->diff(new \DateTime)->y;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseAgeRange(string $ageText): ?array
    {
        if (preg_match('/(\d+)\s*-\s*(\d+)/', $ageText, $matches)) {
            return ['min' => (int) $matches[1], 'max' => (int) $matches[2]];
        }
        if (str_contains(mb_strtolower($ageText), 'mayor') && preg_match('/(\d+)/', $ageText, $matches)) {
            return ['min' => (int) $matches[1], 'max' => 65];
        }
        if (str_contains(mb_strtolower($ageText), 'menor') && preg_match('/(\d+)/', $ageText, $matches)) {
            return ['min' => 18, 'max' => (int) $matches[1]];
        }

        return null;
    }

    private function getScoreSummary(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Excelente compatibilidad - Cumple con prácticamente todos los requisitos',
            $score >= 75 => 'Buena compatibilidad - Cubre los requisitos principales',
            $score >= 60 => 'Compatibilidad moderada - Cumple con requisitos básicos pero tiene áreas de mejora',
            $score >= 40 => 'Compatibilidad baja - Necesita desarrollo en varias áreas clave',
            default => 'Compatibilidad muy baja - No cumple con los requisitos mínimos'
        };
    }

    private function extractEducationLevel(string $education): string
    {
        $education = mb_strtolower($education);
        if (str_contains($education, 'doctorado')) {
            return 'doctorado';
        }
        if (str_contains($education, 'maestría') || str_contains($education, 'maestria')) {
            return 'maestría';
        }
        if (str_contains($education, 'ingeniería') || str_contains($education, 'ingenieria')) {
            return 'ingeniería';
        }
        if (str_contains($education, 'licenciatura')) {
            return 'licenciatura';
        }
        if (str_contains($education, 'universitario')) {
            return 'universitario';
        }

        return 'otro';
    }

    private function extractExperienceLevel(string $experience): string
    {
        $years = $this->extractYears($experience);

        return match (true) {
            $years >= 10 => 'senior',
            $years >= 5 => 'semi-senior',
            $years >= 2 => 'junior',
            default => 'entry'
        };
    }

    private function serializeCandidate($candidate): array
    {
        return [
            'id' => $candidate->id,
            'name' => $candidate->candidate_name,
            'email' => $candidate->candidate_email,
            'phone' => $candidate->candidate_phone,
            'address' => $candidate->address,
            'status' => $candidate->status,
            'skills' => $this->parseSkills($candidate->skills),
            'experience' => $candidate->experience,
            'education' => $candidate->education,
            'age' => $this->calculateAge($candidate->birth_date),
        ];
    }

    private function serializeProfile($profile): array
    {
        return [
            'id' => $profile->id,
            'name' => $profile->name,
            'required_experience' => $profile->experience,
            'required_skills' => $this->parseSkills($profile->skills),
            'age_requirement' => $profile->age,
            'department_id' => $profile->department_id,
        ];
    }

    private function json($payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'profile_id' => $schema->integer()
                ->required()
                ->description('ID del perfil contra el cual comparar candidatos'),
            'limit' => $schema->integer()
                ->nullable()
                ->description('Máximo de candidatos a retornar (default: 10, max: 50)'),
            'min_score' => $schema->number()
                ->nullable()
                ->description('Score mínimo de compatibilidad (0-100) para filtrar resultados'),
            'group_by' => $schema->string()
                ->nullable()
                ->description("Agrupar por: 'status' | 'education' | 'experience'"),
        ];
    }
}

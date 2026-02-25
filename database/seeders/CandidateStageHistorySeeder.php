<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CandidateStageHistory;
use App\Models\Candidate;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CandidateStageHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $candidates = Candidate::all();
        if ($candidates->isEmpty()) {
            $this->command?->warn('No hay candidatos. Ejecuta primero CandidateSeeder.');
            return;
        }

        // Tomamos algunos responsables si existen (reclutadores).
        // Si manejas roles, aquí podrías filtrar por rol=‘recruiter’.
        $responsables = User::take(5)->get(); // si está vacío, dejamos responsable_id en null

        // Distribución de etapas
        $pInterview = 68; // % que pasan a Entrevistado
        $pHired     = 24; // % que llegan a Contratado (solo si fue entrevistado)

        $total = 0;
        $revisados = 0;
        $entrevistados = 0;
        $contratados = 0;

        foreach ($candidates as $candidate) {
            // Fecha base para este candidato: hace entre 10 y 60 días
            $base = Carbon::now()->subDays(random_int(10, 60))->startOfDay();

            // Siempre: Revisado
            $t1 = $base->copy()->addHours(random_int(8, 18));
            $this->upsertStage(
                candidateId: $candidate->id,
                stage: 'Revisado',
                comments: $faker->randomElement([
                    'CV cargado y parseado correctamente.',
                    'Revisión inicial completada.',
                    'Validación de requisitos básicos.',
                    'Pendiente de verificación de referencias.',
                ]),
                responsableId: $responsables->isNotEmpty() ? $responsables->random()->id : null,
                at: $t1
            );
            $revisados++;
            $total++;

            // ¿Pasa a Entrevistado?
            if ($this->hits($pInterview)) {
                $t2 = $t1->copy()->addDays(random_int(1, 7))->addHours(random_int(1, 6));
                $this->upsertStage(
                    candidateId: $candidate->id,
                    stage: 'Entrevistado',
                    comments: $faker->randomElement([
                        'Entrevista técnica realizada. Feedback positivo.',
                        'Entrevista comportamental programada.',
                        'A la espera de prueba técnica.',
                        'Entrevista realizada, pendiente de decisión.',
                    ]),
                    responsableId: $responsables->isNotEmpty() ? $responsables->random()->id : null,
                    at: $t2
                );
                $entrevistados++;
                $total++;

                // ¿Pasa a Contratado?
                if ($this->hits($pHired)) {
                    $t3 = $t2->copy()->addDays(random_int(2, 10))->addHours(random_int(1, 6));
                    $this->upsertStage(
                        candidateId: $candidate->id,
                        stage: 'Contratado',
                        comments: $faker->randomElement([
                            'Oferta aceptada. Incorporación en 2 semanas.',
                            'Aprobación final de dirección.',
                            'Contratación cerrada. Documentación en regla.',
                            'Acepta condiciones. Inicio próximo mes.',
                        ]),
                        responsableId: $responsables->isNotEmpty() ? $responsables->random()->id : null,
                        at: $t3
                    );
                    $contratados++;
                    $total++;
                }
            }
        }

        $this->command?->info("CandidateStageHistorySeeder: $total eventos creados | Revisado=$revisados, Entrevistado=$entrevistados, Contratado=$contratados.");
    }

    /**
     * Inserta o actualiza una fila por (candidate_id, stage) para evitar duplicados.
     * Asegura timestamps cronológicos.
     */
    private function upsertStage(
        int $candidateId,
        string $stage,
        ?string $comments,
        ?int $responsableId,
        Carbon $at
    ): void {
        CandidateStageHistory::updateOrCreate(
            [
                'candidate_id' => $candidateId,
                'stage'        => $stage,
            ],
            [
                'comments'      => $comments,
                'responsable_id' => $responsableId,
                'created_at'    => $at,
                'updated_at'    => $at,
            ]
        );
    }

    /**
     * Bernoulli simple: retorna true con probabilidad p (0..100).
     */
    private function hits(int $p): bool
    {
        return random_int(1, 100) <= $p;
    }
}

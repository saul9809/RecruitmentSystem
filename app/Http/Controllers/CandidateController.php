<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use Inertia\Inertia;

class CandidateController extends Controller
{
    public function index()
    {
        $q = Candidate::query()
            ->from('candidates AS c')
            ->leftJoin('candidate_stage_history AS csh', 'csh.candidate_id', '=', 'c.id')
            ->leftJoin('candidate_experiences AS ce', 'ce.candidate_id', '=', 'c.id')
            ->select('c.*', 'csh.stage AS status', 'ce.position AS last_position');

        $candidates = $q->get();
        return Inertia::render('Candidates/Index', compact('candidates'));
    }

    public function store(Request $request)
    {
        // Lógica para almacenar un nuevo candidato
    }

    public function show($id)
    {
        // Lógica para mostrar los detalles de un candidato específico
    }

    public function update(Request $request, $id)
    {
        // Lógica para actualizar la información de un candidato específico
    }

    public function destroy($id)
    {
        // Lógica para eliminar un candidato específico
    }
}

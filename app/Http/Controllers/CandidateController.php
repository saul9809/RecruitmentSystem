<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;

class CandidateController extends Controller
{
    public function index()
    {
        $candidates = Candidate::all();
        return inertia('Candidates/Index', compact('candidates'));
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

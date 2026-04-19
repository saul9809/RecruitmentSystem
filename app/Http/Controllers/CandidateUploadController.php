<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Domain\Candidates\Services\CandidateIngestionService;

class CandidateUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validación HTTP (responsabilidad de Laravel)
        $request->validate([
            'docs' => ['required', 'array'],
            'docs.*' => ['file', 'mimes:pdf', 'max:10240'], // 10MB
        ]);

        // Delegar al dominio
        $result = app(CandidateIngestionService::class)
            ->ingest($request->file('docs'));

        // Responder vía Inertia
        return Inertia::render('CVProcess/Index', [
            'result' => $result->toArray(),
        ]);
    }
}

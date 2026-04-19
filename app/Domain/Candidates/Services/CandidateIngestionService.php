<?php

namespace App\Domain\Candidates\Services;

use ContentProcessor\Core\ContentProcessor;
use ContentProcessor\Schemas\ArraySchema;
use ContentProcessor\Extractors\PdfTextExtractor;
use ContentProcessor\Structurers\RuleBasedStructurer;
use ContentProcessor\Models\FinalResult;

class CandidateIngestionService
{
    public function ingest(array $files): FinalResult
    {
        //  Cargar schema desde config
        $schemaDefinition = config('cv_schema');

        if (!is_array($schemaDefinition)) {
            throw new \RuntimeException('CV schema configuration is invalid.');
        }

        $schema = new ArraySchema($schemaDefinition);

        //  CONFIGURAR EXTRACTOR + STRUCTURER
        return ContentProcessor::make()
            ->withSchema($schema)
            ->withExtractor(new PdfTextExtractor())
            ->withStructurer(new RuleBasedStructurer())
            ->fromFiles($files)
            ->processFinal();
    }
}

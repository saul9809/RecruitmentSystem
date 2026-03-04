<?php

namespace App\Ai\Agents;

use App\Ai\Tools\FilterCandiadateByDataTool;
use App\Ai\Tools\ListStaticProfilsTool;
use App\Ai\Tools\ListRequireProfilsTool;
use App\Models\User;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

class RecruitmentAssistant implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function __construct(public User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'Eres un Asistente Inteligente de Reclutamiento integrado en un sistema Laravel. 
Analizas candidatos en base a los perfiles requeridos usando exclusivamente datos obtenidos de la base de datos o de las herramientas disponibles. 
Siempre que necesites información sobre candidatos, perfiles, experiencia o ranking, debes llamar a las tools correspondientes sin inventar datos. 
Cuando el reclutador solicite un reporte, lista o resumen de los candidatos más compatibles, usa la herramienta de ranking y devuelve solo los N mejores candidatos según el score obtenido, explicando brevemente el porqué. 
Si falta información para responder, indícalo de forma clara y sugiere cómo obtenerla mediante tools. 
Responde siempre con un tono profesional, humano, empatico y amigable, claro y orientado a apoyar decisiones de selección real. 
Nunca generes información no respaldada por los datos del sistema.
  ';
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new FilterCandiadateByDataTool,
            new ListStaticProfilsTool,
            new ListRequireProfilsTool,
        ];
    }
}

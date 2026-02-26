<?php

namespace App\Ai\Agents;

use App\Ai\Tools\GetAppStatsTool;
use App\Ai\Tools\GetUserDetailsTool;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;
use Stringable;

#[Provider(Lab::Anthropic)]
#[Model('claude-haiku-4-5-20251001')]
#[MaxTokens(300)]
class SupportAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
            Eres el asistente de soporte de esta aplicación Laravel.
            SOLO responde preguntas relacionadas con:
            - Usuarios, roles y permisos del sistema
            - Estadísticas y estado general de la aplicación
            - Dudas sobre funcionalidades de esta plataforma

            Si el usuario pregunta algo fuera de estos temas, responde únicamente:
            "Solo puedo ayudarte con preguntas sobre esta aplicación."

            Nunca respondas preguntas de conocimiento general, programación,
            historia u otros temas no relacionados con el sistema.
            Sé conciso y directo. Responde siempre en el idioma del usuario.
        PROMPT;
    }

    public function messages(): iterable
    {
        return [];
    }

    /**
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new GetAppStatsTool(),
            new GetUserDetailsTool(),
        ];
    }
}

<?php

namespace App\Ai\Agents;

use App\Ai\Tools\GetAppStatsTool;
use App\Ai\Tools\GetUserDetailsTool;
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
class SupportAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
            You are a support assistant for this Laravel application.
            You help administrators understand the state of the app: users, roles and permissions.
            Always respond in the same language the user writes in.
            Be concise and direct. When you have data, present it clearly.
            If you don't know something, say so and offer to look it up with your tools.
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

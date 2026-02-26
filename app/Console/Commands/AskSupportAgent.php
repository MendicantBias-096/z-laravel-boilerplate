<?php

namespace App\Console\Commands;

use App\Ai\Agents\SupportAgent;
use Illuminate\Console\Command;

class AskSupportAgent extends Command
{
    protected $signature = 'ai:ask {message? : El mensaje que le envías al agente}';

    protected $description = 'Envía un mensaje al SupportAgent y muestra la respuesta';

    public function handle(): void
    {
        $message = $this->argument('message')
            ?? $this->ask('¿Qué le quieres preguntar al agente?');

        $this->info('Consultando al agente...');

        $response = (new SupportAgent)->prompt($message);

        $this->newLine();
        $this->line((string) $response);
        $this->newLine();
    }
}

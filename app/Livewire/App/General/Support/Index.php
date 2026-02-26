<?php

namespace App\Livewire\App\General\Support;

use App\Ai\Agents\SupportAgent;
use Livewire\Attributes\Validate;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions;

    #[Validate('required|string|max:500')]
    public string $message = '';

    public array $conversation = [];

    public bool $thinking = false;

    public function send(): void
    {
        $this->validate();

        if (count($this->conversation) >= 20) {
            $this->toast()
                ->warning('Límite alcanzado', 'Limpia la conversación para continuar.')
                ->send();
            return;
        }

        $userMessage = $this->message;
        $this->message = '';

        $this->conversation[] = [
            'role'    => 'user',
            'content' => $userMessage,
        ];

        $response = (new SupportAgent)->prompt($userMessage);

        $this->conversation[] = [
            'role'    => 'agent',
            'content' => (string) $response,
        ];
    }

    public function clear(): void
    {
        $this->conversation = [];
    }

    public function render()
    {
        return view('app.general.support._index');
    }
}

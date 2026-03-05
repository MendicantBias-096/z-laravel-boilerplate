<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use TallStackUi\Traits\Interactions;

class LanguageForm extends Component
{
    use Interactions;

    public string $locale = '';

    public function mount(): void
    {
        $this->locale = auth()->user()->profile?->locale ?? config('app.locale');
    }

    public function save(): void
    {
        $this->validate([
            'locale' => ['required', 'string', 'in:' . implode(',', config('app.supported_locales', ['es', 'en']))],
        ]);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['locale'  => $this->locale]
        );

        $this->toast()
            ->success(__('settings.language_updated'), __('settings.language_saved'))
            ->flash()
            ->send();

        $this->redirect(route('settings'), navigate: false);
    }

    public function render()
    {
        return view('app.general.settings._language-form');
    }
}

<?php

namespace App\Livewire\App\General\Settings;

use App\Enums\Language;
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
            'locale' => ['required', 'string', 'in:' . implode(',', Language::values())],
        ]);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['locale'  => $this->locale]
        );

        $this->toast()
            ->success(__('settings.language_updated'), __('settings.language_saved'))
            ->flash()
            ->send();

        $this->redirect(route('settings'), navigate: true);
    }

    public function render()
    {
        return view('app.general.settings._language-form', [
            'languages' => Language::options(),
        ]);
    }
}

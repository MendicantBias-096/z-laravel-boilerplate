<?php

namespace App\Modules\Platform\Livewire\Settings;

use App\Modules\Platform\Enums\Language;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
            'locale' => ['required', 'string', 'in:'.implode(',', Language::values())],
        ]);

        auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['locale' => $this->locale]
        );

        session(['locale' => $this->locale]);

        $this->toast()
            ->success(__('platform::settings.language_updated'), __('platform::settings.language_saved'))
            ->flash()
            ->send();

        $this->redirect(route('settings'), navigate: true);
    }

    public function render(): Factory|View
    {
        return view('platform::settings._language-form', [
            'languages' => Language::options(),
        ]);
    }
}

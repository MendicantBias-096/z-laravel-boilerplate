<?php

namespace App\Modules\Access\Livewire\Settings;

use App\Modules\Platform\Enums\Language;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use TallStackUi\Traits\Interactions;
use App\Modules\Access\Livewire\Concerns\InteractsWithCurrentUser;

class LanguageForm extends Component
{
    use InteractsWithCurrentUser, Interactions;

    public string $locale = '';

    public function mount(): void
    {
        $this->locale = $this->currentUser()->profile->locale ?? config('app.locale');
    }

    public function save(): void
    {
        $this->validate([
            'locale' => ['required', 'string', 'in:'.implode(',', Language::values())],
        ]);

        $this->currentUser()->profile()->updateOrCreate(
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
        return view('access::settings._language-form', [
            'languages' => Language::options(),
        ]);
    }
}

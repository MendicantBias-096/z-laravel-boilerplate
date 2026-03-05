<?php

namespace App\Livewire\App\General\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class ProfileForm extends Component
{
    use Interactions, WithFileUploads;

    public string $first_name = '';
    public string $last_name  = '';
    public string $locale     = '';

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $photo = null;

    public function mount(): void
    {
        $profile = auth()->user()->profile;

        $this->first_name = $profile?->first_name ?? '';
        $this->last_name  = $profile?->last_name ?? '';
        $this->locale     = $profile?->locale ?? config('app.locale');
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'locale'     => ['required', 'string', 'in:' . implode(',', config('app.supported_locales', ['es', 'en']))],
            'photo'      => ['nullable', 'image', 'max:2048'],
        ]);

        $localeChanged = auth()->user()->profile?->locale !== $this->locale;

        $profile = auth()->user()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['first_name' => $this->first_name, 'last_name' => $this->last_name, 'locale' => $this->locale]
        );

        if ($this->photo) {
            $profile->addMedia($this->photo->getRealPath())
                ->usingFileName($this->photo->getClientOriginalName())
                ->toMediaCollection('photo');

            $this->photo = null;
        }

        $this->dispatch('profile-updated');

        $this->toast()->success('Perfil actualizado', 'Tu información personal ha sido guardada.')->flash()->send();

        if ($localeChanged) {
            $this->redirect(route('settings'), navigate: false);
        }
    }

    public function render()
    {
        return view('app.general.settings._profile-form');
    }
}

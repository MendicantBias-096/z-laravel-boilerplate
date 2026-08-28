<?php

namespace App\Modules\Access\Livewire\Settings;

use App\Modules\Access\Livewire\Concerns\InteractsWithCurrentUser;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class ProfileForm extends Component
{
    use Interactions, InteractsWithCurrentUser, WithFileUploads;

    public string $first_name = '';

    public string $last_name = '';

    /** @var TemporaryUploadedFile|null */
    public $photo;

    public function mount(): void
    {
        $profile = $this->currentUser()->profile;

        // `??` ya cubre el caso de que no haya perfil; el `?->` encima era
        // redundante y PHPStan lo señala como tal.
        $this->first_name = $profile->first_name ?? '';
        $this->last_name = $profile->last_name ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $profile = $this->currentUser()->profile()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['first_name' => $this->first_name, 'last_name' => $this->last_name]
        );

        if ($this->photo) {
            $profile->addMedia($this->photo->getRealPath())
                ->usingFileName($this->photo->getClientOriginalName())
                ->toMediaCollection('photo');

            $this->photo = null;
        }

        $this->dispatch('profile-updated');

        $this->toast()->success(__('platform::settings.profile_updated'), __('platform::settings.profile_saved'))->send();
    }

    public function render(): Factory|View
    {
        return view('access::settings._profile-form');
    }
}

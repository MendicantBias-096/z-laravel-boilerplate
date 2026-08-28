<?php

namespace App\Modules\Platform\Livewire\Settings;

use App\Modules\Platform\Models\Setting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

class SystemForm extends Component
{
    use Interactions, WithFileUploads;

    public string $app_name = '';

    public string $app_short_name = '';

    /** @var TemporaryUploadedFile|null */
    public $logo;

    /** @var TemporaryUploadedFile|null */
    public $favicon;

    public function mount(): void
    {
        $this->authorize('administrar sistema');

        $this->app_name = Setting::get('app_name', config('app.name'));
        $this->app_short_name = Setting::get('app_short_name', '');
    }

    public function save(): void
    {
        $this->validate([
            'app_name' => ['required', 'string', 'max:100'],
            'app_short_name' => ['nullable', 'string', 'max:20'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp,ico', 'max:512'],
        ]);

        Setting::set('app_name', $this->app_name);
        Setting::set('app_short_name', $this->app_short_name);

        if ($this->logo) {
            $ext = $this->logo->getClientOriginalExtension();
            $path = Storage::disk('public')->putFileAs('logo', $this->logo, 'logo.'.$ext);
            Setting::set('logo_path', $path);
            $this->logo = null;
        }

        if ($this->favicon) {
            $ext = $this->favicon->getClientOriginalExtension();
            $path = Storage::disk('public')->putFileAs('logo', $this->favicon, 'favicon.'.$ext);
            Setting::set('favicon_path', $path);
            $this->favicon = null;
        }

        $logoPath = Setting::get('logo_path');
        $faviconPath = Setting::get('favicon_path') ?? $logoPath;
        $displayName = Setting::get('app_short_name') ?: Setting::get('app_name', config('app.name'));

        $this->dispatch('system-updated',
            name: $displayName,
            logoUrl: $logoPath ? Storage::disk('public')->url($logoPath) : null,
            faviconUrl: $faviconPath ? Storage::disk('public')->url($faviconPath) : null,
        );

        $this->toast()->success('Sistema actualizado', 'La configuración ha sido guardada.')->send();
    }

    public function render(): Factory|View
    {
        return view('platform::settings._system-form');
    }
}

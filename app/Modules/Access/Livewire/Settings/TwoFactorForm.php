<?php

namespace App\Modules\Access\Livewire\Settings;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Component;
use TallStackUi\Traits\Interactions;
use App\Modules\Access\Livewire\Concerns\InteractsWithCurrentUser;

class TwoFactorForm extends Component
{
    use InteractsWithCurrentUser, Interactions;

    public bool $showingQr = false;

    public bool $showingRecovery = false;

    public string $confirmationCode = '';

    public string $password = '';

    public function enable(EnableTwoFactorAuthentication $enable): void
    {
        $enable($this->currentUser());
        $this->showingQr = true;
        $this->showingRecovery = false;
    }

    public function confirm(ConfirmTwoFactorAuthentication $confirm): void
    {
        try {
            $confirm($this->currentUser(), $this->confirmationCode);
        } catch (ValidationException) {
            $this->addError('confirmationCode', __('platform::settings.two_factor_invalid_code'));

            return;
        }

        $this->showingQr = false;
        $this->confirmationCode = '';
        $this->showingRecovery = true;

        $this->toast()->success(__('platform::settings.two_factor_enabled'), __('platform::settings.two_factor_enabled_desc'))->send();
    }

    public function showRecoveryCodes(): void
    {
        $this->showingRecovery = ! $this->showingRecovery;
    }

    public function regenerateCodes(GenerateNewRecoveryCodes $generate): void
    {
        $generate($this->currentUser());
        $this->showingRecovery = true;

        $this->toast()->success(__('platform::settings.recovery_codes_regenerated'), __('platform::settings.recovery_codes_regenerated_desc'))->send();
    }

    public function disable(DisableTwoFactorAuthentication $disable): void
    {
        $disable($this->currentUser());

        $this->showingQr = false;
        $this->showingRecovery = false;
        $this->confirmationCode = '';

        $this->toast()->success(__('platform::settings.two_factor_disabled'), __('platform::settings.two_factor_disabled_desc'))->send();
    }

    public function render(): Factory|View
    {
        $user = $this->currentUser();

        return view('access::settings._two-factor-form', [
            'enabled' => ! is_null($user->two_factor_confirmed_at),
            'pending' => ! is_null($user->two_factor_secret) && is_null($user->two_factor_confirmed_at),
            'qrCodeSvg' => ($this->showingQr && ! is_null($user->two_factor_secret))
                ? $user->twoFactorQrCodeSvg()
                : null,
            // La guarda mira la columna que se desencripta. Miraba
            // `two_factor_secret`, otra columna: con secreto pero sin códigos,
            // `decrypt(null)` lanza en vez de mostrar la lista vacía.
            'recoveryCodes' => ($this->showingRecovery && ! is_null($user->two_factor_recovery_codes))
                ? json_decode((string) decrypt($user->two_factor_recovery_codes), true)
                : [],
        ]);
    }
}

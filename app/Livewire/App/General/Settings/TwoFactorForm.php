<?php

namespace App\Livewire\App\General\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class TwoFactorForm extends Component
{
    use Interactions;

    public bool   $showingQr        = false;
    public bool   $showingRecovery  = false;
    public string $confirmationCode = '';
    public string $password         = '';

    public function enable(EnableTwoFactorAuthentication $enable): void
    {
        $enable(Auth::user());
        $this->showingQr = true;
        $this->showingRecovery = false;
    }

    public function confirm(ConfirmTwoFactorAuthentication $confirm): void
    {
        try {
            $confirm(Auth::user(), $this->confirmationCode);
        } catch (ValidationException) {
            $this->addError('confirmationCode', __('settings.two_factor_invalid_code'));
            return;
        }

        $this->showingQr       = false;
        $this->confirmationCode = '';
        $this->showingRecovery = true;

        $this->toast()->success(__('settings.two_factor_enabled'), __('settings.two_factor_enabled_desc'))->send();
    }

    public function showRecoveryCodes(): void
    {
        $this->showingRecovery = ! $this->showingRecovery;
    }

    public function regenerateCodes(GenerateNewRecoveryCodes $generate): void
    {
        $generate(Auth::user());
        $this->showingRecovery = true;

        $this->toast()->success(__('settings.recovery_codes_regenerated'), __('settings.recovery_codes_regenerated_desc'))->send();
    }

    public function disable(DisableTwoFactorAuthentication $disable): void
    {
        $disable(Auth::user());

        $this->showingQr       = false;
        $this->showingRecovery = false;
        $this->confirmationCode = '';

        $this->toast()->success(__('settings.two_factor_disabled'), __('settings.two_factor_disabled_desc'))->send();
    }

    public function render()
    {
        $user = Auth::user();

        return view('app.general.settings._two-factor-form', [
            'enabled'   => ! is_null($user->two_factor_confirmed_at),
            'pending'   => ! is_null($user->two_factor_secret) && is_null($user->two_factor_confirmed_at),
            'qrCodeSvg' => ($this->showingQr && ! is_null($user->two_factor_secret))
                ? $user->twoFactorQrCodeSvg()
                : null,
            'recoveryCodes' => ($this->showingRecovery && ! is_null($user->two_factor_secret))
                ? json_decode(decrypt($user->two_factor_recovery_codes), true)
                : [],
        ]);
    }
}

<div>
    <x-ts-tab selected="{{ __('settings.tab_profile') }}" scroll-on-mobile>

        <x-ts-tab.items tab="{{ __('settings.tab_profile') }}">
            <x-app.settings.tab-profile />
        </x-ts-tab.items>

        <x-ts-tab.items tab="{{ __('settings.tab_system') }}">
            <x-app.settings.tab-system :system="$system" />
        </x-ts-tab.items>

        <x-ts-tab.items tab="{{ __('settings.tab_permissions') }}">
            <x-app.settings.tab-permissions />
        </x-ts-tab.items>

    </x-ts-tab>
</div>

<x-layouts.guest>
    @livewire('auth.reset-password', ['token' => request()->route('token')])
</x-layouts.guest>

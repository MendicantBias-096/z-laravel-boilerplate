<x-layouts.guest>
    @livewire('access::auth.reset-password', ['token' => request()->route('token')])
</x-layouts.guest>

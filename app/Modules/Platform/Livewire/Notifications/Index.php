<?php

namespace App\Modules\Platform\Livewire\Notifications;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

class Index extends Component
{
    use Interactions, WithPagination;

    public string $tab = 'unread';

    public int $quantity = 25;

    public function updatingTab(): void
    {
        $this->resetPage();
    }

    public function updatingQuantity(): void
    {
        $this->resetPage();
    }

    public function markAsRead(string $id): void
    {
        auth()->user()->unreadNotifications()
            ->where('id', $id)
            ->update(['read_at' => now()]);

        $this->dispatch('notifications:refresh');
    }

    public function markAsUnread(string $id): void
    {
        auth()->user()->notifications()
            ->where('id', $id)
            ->update(['read_at' => null]);

        $this->dispatch('notifications:refresh');
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        $this->dispatch('notifications:refresh');
        $this->toast()->success(__('platform::notifications.all_read'), __('platform::notifications.all_read_desc'))->send();
    }

    public function deleteNotification(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->update(['deleted_at' => now()]);

        $this->dispatch('notifications:refresh');
    }

    public function goToNotification(string $id): void
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $this->dispatch('notifications:refresh');

        $url = $notification->data['url'] ?? '/dashboard';
        $this->redirect($url, navigate: true);
    }

    public function render(): Factory|View
    {
        $user = auth()->user();

        $query = $this->tab === 'unread'
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $query->latest()->paginate($this->quantity);

        return view('platform::notifications._index', ['notifications' => $notifications]);
    }
}

<?php

namespace App\Modules\Platform\Livewire\Notifications;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
use TallStackUi\Traits\Interactions;

class NotificationsBell extends Component
{
    use Interactions;

    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('notifications:refresh')]
    public function refreshCount(): void
    {
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
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

    protected function getGroupedNotifications(): Collection
    {
        $notifications = auth()->user()->notifications()->take(6)->get();

        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        return $notifications->groupBy(function ($notification) use ($today, $yesterday): string {
            if ($notification->created_at >= $today) {
                return __('platform::notifications.group_today');
            }
            if ($notification->created_at >= $yesterday) {
                return __('platform::notifications.group_yesterday');
            }

            return __('platform::notifications.group_older');
        });
    }

    public function render(): Factory|View
    {
        return view('platform::livewire.notifications.notifications-bell', [
            'grouped' => $this->getGroupedNotifications(),
        ]);
    }
}

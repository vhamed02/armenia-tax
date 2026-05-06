<?php

namespace App\Livewire\Portal;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserNotifications extends Component
{
    public array $notifications = [];
    public int $unreadCount = 0;

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function markRead(int $id): void
    {
        $notification = Auth::user()->notifications()->find($id);

        if ($notification) {
            $notification->update(['is_read' => true]);
            $this->loadNotifications();
        }
    }

    public function markAllRead(): void
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        $this->loadNotifications();
    }

    private function loadNotifications(): void
    {
        $all = Auth::user()->notifications()->orderByDesc('created_at')->get();

        $this->notifications = $all->map(fn($n) => [
            'id'         => $n->id,
            'type'       => $n->type,
            'title'      => $n->title,
            'message'    => $n->message,
            'is_read'    => $n->is_read,
            'created_at' => $n->created_at->diffForHumans(),
        ])->toArray();

        $this->unreadCount = $all->where('is_read', false)->count();
    }

    public function render()
    {
        return view('livewire.portal.user-notifications')
            ->layout('layouts.portal', ['title' => 'Notifications']);
    }
}

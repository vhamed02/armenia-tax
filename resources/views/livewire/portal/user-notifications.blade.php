<div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        @if($unreadCount > 0)
            <span style="color:#718096;font-size:13px;">{{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}</span>
            <button wire:click="markAllRead" class="btn btn-primary btn-sm">Mark All Read</button>
        @else
            <span style="color:#a0aec0;font-size:13px;">All caught up</span>
        @endif
    </div>

    @forelse($notifications as $n)
        <div style="background:#ffffff;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:16px 20px;margin-bottom:10px;border-left:3px solid {{ !$n['is_read'] ? '#1e88e5' : '#e2e8f0' }};cursor:pointer;"
             wire:click="markRead({{ $n['id'] }})">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:{{ !$n['is_read'] ? '700' : '500' }};color:#1a202c;margin-bottom:4px;">
                        @if($n['type'] === 'tax_alert')
                            🔴
                        @elseif($n['type'] === 'limit_exceeded')
                            ⚠️
                        @else
                            📄
                        @endif
                        {{ $n['title'] }}
                    </div>
                    <div style="font-size:13px;color:#718096;line-height:1.5;">{{ $n['message'] }}</div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:11px;color:#a0aec0;">{{ $n['created_at'] }}</div>
                    @if(!$n['is_read'])
                        <span style="display:inline-block;width:8px;height:8px;background:#1e88e5;border-radius:50%;margin-top:6px;"></span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="padding:40px;text-align:center;color:#a0aec0;">
            No notifications yet.
        </div>
    @endforelse
</div>

@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="notifications-page-container">
    <!-- Header Section -->
    <div class="notifications-header">
        <div class="notifications-header-content">
            <div>
                <h1>Notifications</h1>
                <p class="notifications-header-subtitle">
                    @if($notifications->where('is_read', false)->count() > 0)
                        You have {{ $notifications->where('is_read', false)->count() }} unread notification{{ $notifications->where('is_read', false)->count() != 1 ? 's' : '' }}
                    @else
                        You're all caught up!
                    @endif
                </p>
            </div>
            @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST" style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="button notifications-mark-all-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        Mark All as Read
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($notifications->count() > 0)
        <div class="notifications-list">
            @foreach($notifications as $notification)
                @php
                    $campaignLink = $notification->getCampaignLink();
                    $isUnread = !$notification->is_read;
                @endphp
                <div class="notification-item {{ $isUnread ? 'unread' : 'read' }} {{ $campaignLink ? 'clickable' : '' }}" 
                     data-notification-id="{{ $notification->notification_id }}"
                     data-campaign-link="{{ $campaignLink }}"
                     @if($campaignLink)
                         onclick="markAsReadAndRedirect({{ $notification->notification_id }}, '{{ $campaignLink }}');"
                     @endif>
                    
                    @if($isUnread)
                        <div class="notification-indicator"></div>
                    @endif
                    
                    <div class="notification-content-wrapper">
                        <div class="notification-text-content {{ $isUnread ? 'with-padding' : '' }}">
                            <div class="notification-icon-text">
                                @if($isUnread)
                                    <span class="notification-icon-unread">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3">
                                            <circle cx="12" cy="12" r="10"></circle>
                                        </svg>
                                    </span>
                                @else
                                    <span class="notification-icon-read">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                @endif
                                
                                <p class="notification-message {{ $isUnread ? 'unread' : 'read' }}">
                                    {{ $notification->content }}
                                </p>
                            </div>
                            
                            <div class="notification-timestamp">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <small>{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        @if($isUnread)
                            <form action="{{ route('notifications.markAsRead', $notification->notification_id) }}" 
                                  method="POST" 
                                  class="notification-mark-read-form"
                                  onclick="event.stopPropagation();">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="button button-clear notification-mark-read-btn"
                                        title="Mark as read">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    @if($campaignLink)
                        <div class="notification-arrow-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="notifications-pagination">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="notifications-empty-state">
            <div class="notifications-empty-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0d7db8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
            </div>
            <h3>No Notifications Yet</h3>
            <p>You're all caught up! We'll notify you when something new happens.</p>
        </div>
    @endif
</div>

<script>
function markAsReadAndRedirect(notificationId, url) {
    // Mark as read via AJAX
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    }).then(() => {
        // Redirect to campaign
        window.location.href = url;
    }).catch(() => {
        // Still redirect even if marking as read fails
        window.location.href = url;
    });
}
</script>
@endsection

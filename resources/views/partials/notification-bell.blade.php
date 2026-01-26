<div class="notification-bell" style="position: relative; display: inline-block; margin-left: 1rem; margin-bottom: 0;">
    <button id="notification-bell-btn" 
            style="background: none; border: none; cursor: pointer; padding: 0.5rem; position: relative; display: flex; align-items: center; margin-bottom: 0;"
            title="Notifications">
        <img src="{{ asset('images/bell.png') }}" alt="Notifications" width="22" height="22" style="display: block;" />
        <span id="notification-badge" 
              class="notification-badge" 
              style="display: none; position: absolute; top: 0; right: 0; background: #e74c3c; color: white; border-radius: 10px; padding: 2px 5px; font-size: 0.65rem; font-weight: bold; min-width: 16px; text-align: center; line-height: 1;">
        </span>
    </button>

    <div id="notification-dropdown" 
         class="notification-dropdown {{ request()->routeIs('campaigns.show') ? 'campaign-page-dropdown' : '' }}" 
         style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.5rem; width: 320px; max-height: 400px; overflow-y: auto; background: white; border: 1px solid #d1d1d1; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); z-index: 1000;">
        
        <div style="padding: 1rem; border-bottom: 1px solid #e1e1e1; display: flex; justify-content: space-between; align-items: center;">
            <strong>Notifications</strong>
            <a href="{{ route('notifications.index') }}" style="font-size: 0.85rem;">View All</a>
        </div>

        <div id="notification-list" style="max-height: 300px; overflow-y: auto;">
            <div style="padding: 2rem; text-align: center; color: #888;">
                Loading...
            </div>
        </div>
    </div>
</div>

<style>
.notification-dropdown {
    scrollbar-width: thin;
}

.notification-dropdown::-webkit-scrollbar {
    width: 6px;
}

.notification-dropdown::-webkit-scrollbar-thumb {
    background: #d1d1d1;
    border-radius: 3px;
}

.notification-item-preview {
    padding: 1rem;
    border-bottom: 1px solid #f1f1f1;
    cursor: pointer;
    transition: background 0.2s;
}

.notification-item-preview:hover {
    background: #f9f9f9;
}

.notification-item-preview.unread {
    background: #f8f9ff;
}

.notification-item-preview p {
    margin: 0 0 0.3rem 0;
    font-size: 0.9rem;
    line-height: 1.4;
}

.notification-item-preview small {
    color: #888;
    font-size: 0.75rem;
}

/* Bell hover animation (same as follow button) */
#notification-bell-btn img { transition: transform 380ms cubic-bezier(.2,.9,.2,1); }
#notification-bell-btn:hover img { transform: rotate(-15deg); animation: bell-wiggle 420ms ease; }
@keyframes bell-wiggle { 0% { transform: rotate(0deg); } 30% { transform: rotate(-15deg); } 60% { transform: rotate(8deg); } 100% { transform: rotate(0deg); } }

/* Campaign page: dropdown appears above bell (header is at bottom) */
.campaign-page-dropdown {
    top: auto !important;
    bottom: 100% !important;
    margin-top: 0 !important;
    margin-bottom: 0.5rem !important;
}
</style>

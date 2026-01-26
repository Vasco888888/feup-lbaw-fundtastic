@extends('layouts.app')

@section('title', $user->name . ' - Profile')

@section('content')
<div class="profile-shell enhanced-profile">
    @php
        $isUserOwner = Auth::check() && isset($user->user_id) && Auth::id() == $user->user_id;
        $isAdminOwner = Auth::guard('admin')->check() && isset($user->admin_id) && Auth::guard('admin')->id() == $user->admin_id;
        $isAdminProfile = isset($user->admin_id) || (is_object($user) && $user instanceof \App\Models\Admin);
    @endphp

    <!-- Profile Header Section -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar enhanced-avatar">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image }}" alt="{{ $user->name }}'s avatar" />
                    @else
                        <div class="avatar-placeholder">
                            <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="40" cy="30" r="15" fill="#1aa37a" opacity="0.3"/>
                                <path d="M20 65 C20 50, 30 45, 40 45 C50 45, 60 50, 60 65" fill="#1aa37a" opacity="0.3"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            <div class="profile-header-info">
                <div class="profile-title-row">
                    <h1 class="profile-name">
                        {{ $user->name }}
                        @if(isset($isAdminProfile) && $isAdminProfile)
                            <span class="admin-badge">Admin</span>
                        @endif
                        @if(($user->banned ?? false))
                            <span class="banned-badge">Banned</span>
                        @endif
                    </h1>
                    <div class="profile-actions">
                        @if($isUserOwner || $isAdminOwner)
                            <a href="{{ $isUserOwner ? route('profile.edit') : route('admin.profile.edit') }}" class="button button-outline-modern">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path d="M12.854 1.146a.5.5 0 0 0-.707 0L10.5 2.793 13.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-2-2zm-2.146 2.146L3 11v3h3l7.708-7.708-3-3z"/>
                                </svg>
                                Edit Profile
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="button button-secondary-modern">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                                        <path d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        @endif
                        @if(Auth::guard('admin')->check() && empty($isAdminProfile))
                            <form action="{{ route('admin.users.toggleBan', ['id' => $user->user_id ?? $user->id]) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="button button-danger-modern">{{ ($user->banned ?? false) ? 'Unban User' : 'Ban User' }}</button>
                            </form>
                            <form action="{{ route('admin.users.destroy', ['id' => $user->user_id ?? $user->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete this user? This will anonymize their content and remove the account.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button button-danger-modern">Delete User</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="profile-stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Member Since</div>
                            <div class="stat-value">{{ optional($user->created_at)->format('M Y') ?? '—' }}</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Email</div>
                            <div class="stat-value">{{ $user->email }}</div>
                        </div>
                    </div>

                    @unless($isAdminProfile)
                    <div class="stat-card highlight">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Total Contributions</div>
                            <div class="stat-value">${{ number_format($totalContributions, 2) }}</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Campaigns Created</div>
                            <div class="stat-value">{{ $campaigns->count() }}</div>
                        </div>
                    </div>
                    @endunless
                </div>
            </div>
        </div>
    </div>

    <!-- About Section -->
    @if($user->bio || $isUserOwner || $isAdminOwner)
    <div class="profile-section">
        <div class="section-header">
            <h3 class="section-title">About</h3>
        </div>
        <div class="about-content">
            @if($user->bio)
                <p class="bio-text">{!! nl2br(e($user->bio)) !!}</p>
            @else
                <p class="empty-state-text">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.3">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    No bio provided yet.
                </p>
            @endif
        </div>
    </div>
    @endif

    <!-- Activity Sections -->
    @unless($isAdminProfile)
    <div class="profile-activities">
        <!-- Contributions Section -->
        <div class="activity-section">
            <div class="section-header">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    My Contributions
                    <span class="count-badge">{{ $donations->count() }}</span>
                </h3>
            </div>
            
            <div class="activity-content">
                @if($donations->isEmpty())
                    <div class="empty-state">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.2">
                            <line x1="12" y1="1" x2="12" y2="23"/>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                        </svg>
                        <p>No contributions yet</p>
                        <small>Start supporting campaigns to see them here</small>
                    </div>
                @else
                    <div class="activity-list">
                        @foreach($donations as $donation)
                            @php $camp = $donation->campaign; @endphp
                            <div class="activity-item {{ $loop->index >= 4 ? 'extra-donation' : '' }}" @if($loop->index >= 4) style="display:none" @endif>
                                <div class="activity-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                    </svg>
                                </div>
                                <div class="activity-details">
                                    <div class="activity-title">
                                        @if($camp)
                                            <a href="{{ route('campaigns.show', $camp->campaign_id) }}" class="activity-link">
                                                {{ $camp->title ?? 'Campaign' }}
                                            </a>
                                        @else
                                            <span class="activity-link-disabled">{{ $donation->campaign?->title ?? 'Deleted campaign' }}</span>
                                        @endif
                                        @if(isset($donation->is_valid) && !$donation->is_valid)
                                            <span class="invalid-badge">Invalid</span>
                                        @endif
                                    </div>
                                    <div class="activity-meta">
                                        <span class="activity-amount">${{ number_format($donation->amount, 2) }}</span>
                                        <span class="activity-date">{{ optional($donation->date)->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($donations->count() > 4)
                        <div class="activity-footer">
                            <button id="toggle-donations" class="toggle-button">
                                View all contributions ({{ $donations->count() - 4 }} more)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Campaigns Section -->
        <div class="activity-section">
            <div class="section-header">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    My Campaigns
                    <span class="count-badge">{{ $campaigns->count() }}</span>
                </h3>
            </div>
            
            <div class="activity-content">
                @if($campaigns->isEmpty())
                    <div class="empty-state">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <p>No campaigns yet</p>
                        <small>Create your first campaign to see it here</small>
                    </div>
                @else
                    <div class="campaign-grid">
                        @foreach($campaigns as $camp)
                            <div class="campaign-card {{ $loop->index >= 3 ? 'extra-campaign' : '' }}" @if($loop->index >= 3) style="display:none" @endif>
                                <a href="{{ route('campaigns.show', $camp->campaign_id) }}" class="campaign-card-link">
                                    <h4 class="campaign-card-title">{{ $camp->title }}</h4>
                                    
                                    <div class="campaign-progress-section">
                                        <div class="progress-bar-container">
                                            <div class="progress-bar-fill" style="width: {{ $camp->goal_amount > 0 ? min(100, ($camp->current_amount / $camp->goal_amount) * 100) : 0 }}%"></div>
                                        </div>
                                        <div class="campaign-stats">
                                            <div class="campaign-stat">
                                                <span class="stat-value-sm">${{ number_format($camp->current_amount, 2) }}</span>
                                                <span class="stat-label-sm">raised</span>
                                            </div>
                                            <div class="campaign-stat">
                                                <span class="stat-value-sm">{{ $camp->donations_count ?? 0 }}</span>
                                                <span class="stat-label-sm">backers</span>
                                            </div>
                                            <div class="campaign-stat">
                                                <span class="stat-value-sm">{{ $camp->goal_amount > 0 ? round(($camp->current_amount / $camp->goal_amount) * 100) : 0 }}%</span>
                                                <span class="stat-label-sm">funded</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($campaigns->count() > 3)
                        <div class="activity-footer">
                            <button id="toggle-campaigns" class="toggle-button">
                                View all campaigns ({{ $campaigns->count() - 3 }} more)
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endunless
</div>

<script>
(function() {
    'use strict';
    
    function toggleItems(buttonId, itemClass, singularLabel) {
        const button = document.getElementById(buttonId);
        if (!button) return;
        
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const items = document.querySelectorAll('.' + itemClass);
            const isExpanded = button.classList.contains('expanded');
            
            items.forEach(function(item) {
                if (isExpanded) {
                    item.style.display = 'none';
                } else {
                    item.style.display = '';
                }
            });
            
            if (isExpanded) {
                button.classList.remove('expanded');
                button.innerHTML = `View all ${singularLabel} (${items.length} more) <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>`;
            } else {
                button.classList.add('expanded');
                button.innerHTML = `View less <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="18 15 12 9 6 15"/></svg>`;
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            toggleItems('toggle-donations', 'extra-donation', 'contributions');
            toggleItems('toggle-campaigns', 'extra-campaign', 'campaigns');
        });
    } else {
        toggleItems('toggle-donations', 'extra-donation', 'contributions');
        toggleItems('toggle-campaigns', 'extra-campaign', 'campaigns');
    }
})();
</script>

<style>
/* Enhanced Profile Styles */
.enhanced-profile {
    background: linear-gradient(135deg, #ffffff 0%, #f8fdfb 100%);
}

.profile-header {
    background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
    border-radius: 0.85rem;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(13, 125, 184, 0.15);
}

.profile-header-content {
    display: flex;
    gap: 2.5rem;
    align-items: flex-start;
}

.profile-avatar-wrapper {
    flex-shrink: 0;
}

.enhanced-avatar {
    width: 140px;
    height: 140px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
}

.enhanced-avatar:hover {
    transform: scale(1.05);
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.9);
}

.profile-header-info {
    flex: 1;
    color: white;
}

.profile-title-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
}

.profile-name {
    font-size: 2.25rem;
    font-weight: 700;
    margin: 0;
    color: white;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.profile-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.button-outline-modern {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.4);
    color: white;
    backdrop-filter: blur(10px);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    transition: all 0.3s ease;
}

.button-outline-modern:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.6);
    transform: translateY(-2px);
}

.button-secondary-modern {
    background: rgba(255, 255, 255, 0.9);
    color: #0d7db8;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.65rem 1.25rem;
    transition: all 0.3s ease;
}

.button-secondary-modern:hover {
    background: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.button-danger-modern {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.65rem 1.25rem;
    transition: all 0.3s ease;
    text-align: center;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.button-danger-modern:hover {
    background: #c82333;
    transform: translateY(-2px);
}

.profile-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 0.75rem;
    padding: 1.25rem;
    display: flex;
    gap: 1rem;
    align-items: center;
    transition: all 0.3s ease;
}

.stat-card:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.stat-card.highlight {
    background: rgba(255, 255, 255, 0.25);
    border: 2px solid rgba(255, 255, 255, 0.4);
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.stat-content {
    flex: 1;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    margin-bottom: 0.25rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    word-break: break-word;
    overflow-wrap: break-word;
    line-height: 1.3;
}

.profile-section {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e6f0ea;
    box-shadow: 0 4px 12px rgba(9, 64, 94, 0.04);
}

.section-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e6f0ea;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0d7db8;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.count-badge {
    background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 600;
}

.about-content {
    padding: 1rem 0;
}

.bio-text {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #2c3e50;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: pre-wrap;
}

.empty-state-text {
    text-align: center;
    padding: 3rem 1rem;
    color: #95a5a6;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.profile-activities {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
}

.activity-section {
    background: white;
    border-radius: 0.75rem;
    padding: 2rem;
    border: 1px solid #e6f0ea;
    box-shadow: 0 4px 12px rgba(9, 64, 94, 0.04);
    display: flex;
    flex-direction: column;
}

.activity-content {
    flex: 1;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #95a5a6;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.empty-state p {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

.empty-state small {
    font-size: 0.9rem;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.activity-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8fdfb;
    border: 1px solid #e6f0ea;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
}

.activity-item:hover {
    background: #eef8f4;
    border-color: #1aa37a;
    transform: translateX(4px);
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.activity-details {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.activity-link {
    color: #0d7db8;
    text-decoration: none;
    transition: color 0.2s ease;
}

.activity-link:hover {
    color: #1aa37a;
    text-decoration: underline;
}

.activity-link-disabled {
    color: #95a5a6;
}

.invalid-badge {
    background: #f8d7da;
    color: #721c24;
    padding: 0.15rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
}

.activity-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.9rem;
    color: #587086;
}

.activity-amount {
    font-weight: 700;
    color: #1aa37a;
}

.campaign-grid {
    display: grid;
    gap: 1rem;
}

.campaign-card {
    background: #f8fdfb;
    border: 1px solid #e6f0ea;
    border-radius: 0.5rem;
    padding: 1.25rem;
    transition: all 0.3s ease;
}

.campaign-card:hover {
    background: #eef8f4;
    border-color: #1aa37a;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(9, 64, 94, 0.12);
}

.campaign-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.campaign-card-title {
    color: #0d7db8;
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 1rem 0;
    transition: color 0.2s ease;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.campaign-card:hover .campaign-card-title {
    color: #1aa37a;
}

.campaign-progress-section {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.progress-bar-container {
    background: #dff2e8;
    height: 8px;
    border-radius: 999px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #0d7db8, #1aa37a);
    border-radius: 999px;
    transition: width 0.6s ease;
}

.campaign-stats {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
}

.campaign-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-value-sm {
    font-size: 1rem;
    font-weight: 700;
    color: #0d7db8;
}

.stat-label-sm {
    font-size: 0.75rem;
    color: #587086;
}

.activity-footer {
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e6f0ea;
    display: flex;
    justify-content: center;
}

.toggle-button {
    background: transparent;
    border: 2px solid #1aa37a;
    color: #1aa37a;
    padding: 0.65rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.toggle-button:hover {
    background: #1aa37a;
    color: white;
    transform: translateY(-2px);
}

.toggle-button.expanded svg {
    transform: rotate(180deg);
}

.toggle-button svg {
    transition: transform 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
    .profile-header {
        padding: 1.5rem;
    }
    
    .profile-header-content {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .profile-title-row {
        flex-direction: column;
        align-items: center;
    }
    
    .profile-name {
        font-size: 1.75rem;
        justify-content: center;
    }
    
    .profile-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .profile-activities {
        grid-template-columns: 1fr;
    }
    
    .campaign-stats {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .profile-section, .activity-section {
        padding: 1.25rem;
    }
    
    .profile-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .profile-actions .button,
    .profile-actions form {
        width: 100%;
    }
    
    .profile-actions button {
        width: 100%;
    }
}
</style>
@endsection

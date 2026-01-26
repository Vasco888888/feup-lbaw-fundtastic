@extends('layouts.app')

@section('title', $campaign->title . ' - FundTastic')

@section('content')
<div class="campaign-page-modern">
    {{-- Hero Section with Cover Image --}}
    <div class="campaign-hero-section">
        @php
            $coverMedia = $campaign->coverMedia;
            $coverUrl = $coverMedia ? asset($coverMedia->file_path) : asset('images/logo.png');
            $isOwnerOrCollaborator = Auth::check() && (Auth::id() === $campaign->creator_id || $campaign->collaborators->contains('user_id', Auth::id()));
        @endphp
        <div class="hero-image-container" style="background-image: url('{{ $coverUrl }}');"></div>
        
        {{-- Status Badge --}}
        @if($campaign->status !== 'active')
            <div class="status-badge status-{{ $campaign->status }}">
                @if($campaign->status === 'completed')
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Completed
                @else
                    {{ ucfirst($campaign->status) }}
                @endif
            </div>
        @endif
    </div>

    <div class="campaign-container">
        {{-- Main Content --}}
        <div class="campaign-content-grid">
            {{-- Left Column: Main Info --}}
            <div class="campaign-main-col">
                {{-- Header --}}
                <header class="campaign-header-modern">
                    <div class="category-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" stroke="currentColor" stroke-width="2"/>
                            <circle cx="7" cy="7" r="1" fill="currentColor"/>
                        </svg>
                        {{ $campaign->category?->name ?? 'Uncategorized' }}
                    </div>
                    
                    <h1 class="campaign-title-modern">{{ $campaign->title }}</h1>
                    
                    <div class="creator-info-modern">
                        @if($campaign->creator)
                            <a href="{{ route('users.show', $campaign->creator->user_id) }}" class="creator-link-modern">
                                <img src="{{ $campaign->creator->profile_image ?? asset('images/defaultpfp.svg') }}" alt="{{ $campaign->creator->name }}" />
                                <div>
                                    <span class="creator-label-small">Created by</span>
                                    <strong>{{ $campaign->creator->name }}</strong>
                                </div>
                            </a>
                        @else
                            <div class="creator-link-modern">
                                <img src="{{ asset('images/defaultpfp.svg') }}" alt="Anonymous" />
                                <div>
                                    <span class="creator-label-small">Created by</span>
                                    <strong>Anonymous</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </header>

                {{-- Collaborators Section (Visible to Everyone) --}}
                @if($campaign->collaborators->count() > 0)
                    <section style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <h2 style="color: #0369a1; font-size: 1rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin: 0 0 1rem 0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Collaborators ({{ $campaign->collaborators->count() }}/5)
                        </h2>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.75rem;">
                            @foreach($campaign->collaborators as $collaborator)
                                <div style="background: white; border-radius: 8px; padding: 0.75rem; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; position: relative;">
                                    @if(Auth::check() && Auth::id() === $campaign->creator_id)
                                        <form method="POST" action="{{ route('collaboration.remove', $campaign->campaign_id) }}" style="position: absolute; top: 0.5rem; right: 0.5rem; margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="collaborator_id" value="{{ $collaborator->user_id }}">
                                            <button type="submit" onclick="return confirm('Remove {{ $collaborator->name }} as collaborator?');" style="width: 24px; height: 24px; padding: 0; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 50%; font-size: 0.75rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'" title="Remove collaborator">
                                                ×
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('users.show', $collaborator->user_id) }}" style="text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 0.5rem;">
                                        <img src="{{ $collaborator->profile_image ?? asset('images/defaultpfp.svg') }}" 
                                             alt="{{ $collaborator->name }}" 
                                             style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #bae6fd;" />
                                        <strong style="color: #1a1a1a; font-size: 0.875rem; text-align: center;">{{ $collaborator->name }}</strong>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- About Section --}}
                <section class="about-section-modern">
                    <h2 class="section-title-modern">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                            <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2"/>
                            <line x1="16" y1="13" x2="8" y2="13" stroke="currentColor" stroke-width="2"/>
                            <line x1="16" y1="17" x2="8" y2="17" stroke="currentColor" stroke-width="2"/>
                            <polyline points="10 9 9 9 8 9" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        About This Campaign
                    </h2>
                    <p class="campaign-description-modern">{{ $campaign->description }}</p>
                </section>

                {{-- Media Gallery --}}
                @if(($campaign->media && $campaign->media->count() > 0) || $isOwnerOrCollaborator)
                    <section class="media-section-modern">
                        <div class="section-header-flex">
                            <h2 class="section-title-modern">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <rect x="3" y="3" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="8.5" cy="8.5" r="1.5" fill="currentColor"/>
                                    <polyline points="21 15 16 10 5 21" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Gallery
                            </h2>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($campaign->media && $campaign->media->count() > 0)
                                    <span class="media-count-badge">{{ $campaign->media->count() }} {{ $campaign->media->count() === 1 ? 'item' : 'items' }}</span>
                                @endif
                                @if($isOwnerOrCollaborator && (!$campaign->media || $campaign->media->count() < 5))
                                    <button type="button" id="add-media-btn" class="btn-icon-action" title="Add Media">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                            <line x1="12" y1="8" x2="12" y2="16" stroke="currentColor" stroke-width="2"/>
                                            <line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                        @if($campaign->media && $campaign->media->count() > 0)
                            <div class="media-grid-modern">
                                @foreach($campaign->media as $media)
                                    @include('partials.campaign.media', ['media' => $media])
                                @endforeach
                            </div>
                        @else
                            <p class="empty-state-text">No media yet. Add photos, videos or documents to showcase your campaign.</p>
                        @endif
                    </section>
                @endif

                {{-- Hidden upload form for media --}}
                @if($isOwnerOrCollaborator)
                    <form id="media-upload-form" method="POST" action="{{ route('campaigns.media.store', $campaign->campaign_id) }}" enctype="multipart/form-data" style="display:none;">
                        @csrf
                        <input type="file" name="media" id="media-file-input" accept="image/*,video/*,application/pdf" />
                    </form>
                @endif

                {{-- Updates Section --}}
                <section class="updates-section-modern">
                    <div class="section-header-flex">
                        <h2 class="section-title-modern">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Latest Updates
                        </h2>
                        @if($isOwnerOrCollaborator)
                            <button type="button" class="btn-icon-action" onclick="document.getElementById('update-modal').style.display='block'" title="Post Update">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <line x1="12" y1="8" x2="12" y2="16" stroke="currentColor" stroke-width="2"/>
                                    <line x1="8" y1="12" x2="16" y2="12" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                    @if($recentUpdates->isEmpty())
                        <p class="empty-state-text">No updates yet. Stay tuned!</p>
                    @else
                        <div class="updates-list-modern">
                            @each('partials.campaign.update', $recentUpdates, 'update')
                        </div>
                    @endif
                </section>

                {{-- Comments Section --}}
                <section class="comments-section-modern">
                    <div class="section-header-flex">
                        <h2 class="section-title-modern">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Community ({{ $recentComments->count() }})
                        </h2>
                        @auth
                            <button type="button" class="btn-icon-action" onclick="document.getElementById('comment-modal').style.display='block'" title="Post Comment">
                                <img src="{{ asset('images/chat.png') }}" alt="Post Comment" width="24" height="24" style="display:block;" />
                            </button>
                        @endauth
                    </div>
                    @if($recentComments->isEmpty())
                        <p class="empty-state-text">No comments yet. Start the conversation!</p>
                    @else
                        <div class="comments-list-modern">
                            @each('partials.campaign.comment', $recentComments, 'comment')
                        </div>
                    @endif
                </section>
            </div>

            {{-- Right Column: Sidebar --}}
            <aside class="campaign-sidebar-modern">
                {{-- Actions Card --}}
                <div class="sidebar-card actions-card">
                    @if(!Auth::guard('admin')->check() && Auth::check() && !$isOwnerOrCollaborator && $campaign->status === 'active' && (is_null($campaign->start_date) || $campaign->start_date->lte(now())))
                        <button type="button" id="donate-btn" class="btn-donate-modern" onclick="document.getElementById('donate-modal').style.display='block'">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" fill="currentColor"/>
                            </svg>
                            Donate Now
                        </button>
                    @endif

                    <div class="action-buttons-row">
                        <button type="button" id="share-btn" class="btn-action-secondary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <circle cx="18" cy="5" r="3" stroke="currentColor" stroke-width="2"/>
                                <circle cx="6" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                                <circle cx="18" cy="19" r="3" stroke="currentColor" stroke-width="2"/>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49" stroke="currentColor" stroke-width="2"/>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Share
                        </button>

                        @if(!Auth::guard('admin')->check() && Auth::check() && !$isOwnerOrCollaborator)
                            @if(isset($isFollowing) && $isFollowing)
                                <form method="POST" action="{{ route('api.campaigns.unfollow', $campaign->campaign_id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-secondary following">
                                        <img src="{{ asset('images/bell.png') }}" alt="Following" width="18" height="18" style="display: block;" />
                                        Following
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('api.campaigns.follow', $campaign->campaign_id) }}">
                                    @csrf
                                    <button type="submit" class="btn-action-secondary">
                                        <img src="{{ asset('images/bell.png') }}" alt="Follow" width="18" height="18" style="display: block;" />
                                        Follow
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>

                    @if(!Auth::guard('admin')->check() && Auth::check() && !$isOwnerOrCollaborator)
                        <button type="button" class="btn-report-campaign report-btn" data-target-type="campaign" data-target-id="{{ $campaign->campaign_id }}" title="Report Campaign">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke="currentColor" stroke-width="2"/>
                                <line x1="4" y1="22" x2="4" y2="15" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            Report Campaign
                        </button>

                        {{-- Collaboration Request Button --}}
                        @if($campaign->collaborators->count() < 5 && !$hasPendingRequest && !$campaign->collaborators->contains('user_id', Auth::id()))
                            <button type="button" id="request-collab-btn" style="width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; transition: all 0.3s; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.2)'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                    <line x1="20" y1="8" x2="20" y2="14" stroke="currentColor" stroke-width="2"/>
                                    <line x1="23" y1="11" x2="17" y2="11" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Request to Collaborate
                            </button>
                        @elseif($hasPendingRequest)
                            <div style="width: 100%; padding: 0.75rem; background: #fef3c7; border: 1px solid #fde047; color: #92400e; border-radius: 8px; font-weight: 600; margin-top: 0.75rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                    <polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Request Pending
                            </div>
                        @endif
                    @endif

                    @if($isOwnerOrCollaborator)
                        <div class="creator-badge-sidebar">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor"/>
                            </svg>
                            {{ Auth::id() === $campaign->creator_id ? 'Your Campaign' : 'Collaborating' }}
                        </div>

                        {{-- Pending Collaboration Requests --}}
                        @if($pendingRequests->isNotEmpty())
                            <div style="background: #fef3c7; border: 1px solid #fde047; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; margin-top: 1.5rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <strong style="color: #92400e; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                                            <circle cx="8.5" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                            <line x1="20" y1="8" x2="20" y2="14" stroke="currentColor" stroke-width="2"/>
                                            <line x1="23" y1="11" x2="17" y2="11" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        Collaboration Requests ({{ $pendingRequests->count() }})
                                    </strong>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                    @foreach($pendingRequests as $collabRequest)
                                        <div style="background: white; border-radius: 6px; padding: 0.75rem;">
                                            <a href="{{ route('users.show', $collabRequest->requester->user_id) }}" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none; margin-bottom: 0.5rem;">
                                                <img src="{{ $collabRequest->requester->profile_image ?? asset('images/defaultpfp.svg') }}" 
                                                     alt="{{ $collabRequest->requester->name }}" 
                                                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;" />
                                                <div>
                                                    <strong style="color: #1a1a1a; font-size: 0.875rem; display: block;">{{ $collabRequest->requester->name }}</strong>
                                                    <span style="color: #666; font-size: 0.75rem;">{{ $collabRequest->created_at->diffForHumans() }}</span>
                                                </div>
                                            </a>
                                            @if($collabRequest->message)
                                                <p style="color: #374151; font-size: 0.75rem; margin: 0.5rem 0; font-style: italic; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">"{{ $collabRequest->message }}"</p>
                                            @endif
                                            <div style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                                                <form method="POST" action="{{ route('collaboration.accept', $collabRequest->request_id) }}" style="margin: 0; flex: 1;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" style="width: 100%; padding: 0.4rem; background: #10b981; color: white; border: none; border-radius: 4px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                                                        Accept
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('collaboration.reject', $collabRequest->request_id) }}" style="margin: 0; flex: 1;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" style="width: 100%; padding: 0.4rem; background: #ef4444; color: white; border: none; border-radius: 4px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if($campaign->donations->isEmpty())
                            <div class="creator-controls">
                                <a href="{{ route('campaigns.edit', $campaign->campaign_id) }}" class="btn-creator-action">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" stroke="currentColor" stroke-width="2"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    Edit Campaign
                                </a>
                                @if(Auth::id() === $campaign->creator_id)
                                    <form method="POST" action="{{ route('campaigns.destroy', $campaign->campaign_id) }}" onsubmit="return confirm('Delete this campaign? This action cannot be undone.');" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-creator-delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <polyline points="3 6 5 6 21 6" stroke="currentColor" stroke-width="2"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="2"/>
                                                <line x1="10" y1="11" x2="10" y2="17" stroke="currentColor" stroke-width="2"/>
                                                <line x1="14" y1="11" x2="14" y2="17" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    @endif

                    @if(Auth::guard('admin')->check())
                        <div class="admin-actions">
                            @if($campaign->status !== 'completed')
                                <form method="POST" action="{{ route('admin.campaigns.toggleSuspend', $campaign->campaign_id) }}">
                                    @csrf
                                    <button type="submit" class="btn-admin">{{ $campaign->status === 'suspended' ? 'Activate' : 'Suspend' }}</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.campaigns.destroy', $campaign->campaign_id) }}" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-admin-danger">Delete Campaign</button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Progress Card --}}
                <div class="sidebar-card progress-card">
                    <div class="progress-amount">
                        <span class="amount-raised">€{{ number_format((float) $campaign->current_amount, 2, '.', ',') }}</span>
                        <span class="amount-goal">raised of €{{ number_format((float) $campaign->goal_amount, 2, '.', ',') }}</span>
                    </div>
                    
                    <div class="progress-bar-modern">
                        <div class="progress-fill-modern" style="width: {{ min($progress, 100) }}%"></div>
                    </div>
                    
                    <div class="stats-grid-modern">
                        <div class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                                <polyline points="12 6 12 12 16 14" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="stat-value">{{ number_format($progress, 0) }}%</span>
                            <span class="stat-label">Progress</span>
                        </div>
                        <div class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2"/>
                                <circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="stat-value">{{ $campaign->donations->count() }}</span>
                            <span class="stat-label">Supporters</span>
                        </div>
                        <div class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="stat-value">{{ $campaign->popularity ?? 0 }}</span>
                            <span class="stat-label">Popularity</span>
                        </div>
                        <div class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="stat-value">{{ $campaign->start_date?->format('M d') ?? '—' }}</span>
                            <span class="stat-label">Started</span>
                        </div>
                        <div class="stat-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="2"/>
                                <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2"/>
                                <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="stat-value">{{ $campaign->end_date?->format('M d') ?? '—' }}</span>
                            <span class="stat-label">Ends</span>
                        </div>
                    </div>
                </div>

                {{-- Recent Donations Card --}}
                <div class="sidebar-card donations-card">
                    <h3 class="sidebar-card-title">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2"/>
                        </svg>
                        Recent Donations
                    </h3>
                    @if($recentDonations->isEmpty())
                        <p class="empty-state-text-small">Be the first to contribute!</p>
                    @else
                        <div class="donations-list-modern">
                            @each('partials.campaign.donation', $recentDonations, 'donation')
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</div>

{{-- Donate Modal --}}
@if(!Auth::guard('admin')->check())
    @auth
        @if(!$isOwnerOrCollaborator && $campaign->status === 'active' && (is_null($campaign->start_date) || $campaign->start_date->lte(now())))
            <div id="donate-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
                <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2.5rem; max-width: 540px; width: 90%; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.75rem; font-weight: 700; color: #059669; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2"/>
                                </svg>
                            </div>
                            Support this campaign
                        </h3>
                        <button type="button" onclick="document.getElementById('donate-modal').style.display='none'" style="background: #f3f4f6; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#e5e7eb'; this.style.color='#374151'" onmouseout="this.style.background='#f3f4f6'; this.style.color='#6b7280'">&times;</button>
                    </div>
                    
                    <form id="donate-form" method="POST" action="{{ route('api.campaigns.donate', $campaign->campaign_id) }}">
                        @csrf
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label for="amount" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9375rem;">Amount (€) *</label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0.01" required style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9375rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <label for="message" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9375rem;">Message (optional)</label>
                            <textarea id="message" name="message" rows="3" maxlength="500" style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9375rem; font-family: inherit; resize: vertical; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem; display:flex; align-items:center; gap:0.75rem;">
                            <input type="hidden" name="is_anonymous" value="0">
                            <label for="is_anonymous" style="display:inline-flex; align-items:center; gap:0.6rem; cursor:pointer; font-weight: 600; color: #374151; font-size: 0.9375rem;">
                                <span>Contribute anonymously</span>
                                <span class="switch" aria-hidden="true" style="display:inline-block; position:relative; width:44px; height:26px;">
                                    <span class="switch-track" style="position:absolute; inset:0; background:#e6e6e6; border-radius:999px; transition:background 180ms;"></span>
                                    <span class="switch-thumb" style="position:absolute; left:3px; top:3px; width:20px; height:20px; background:#fff; border-radius:50%; box-shadow:0 2px 6px rgba(2,6,23,0.12); transition:left 180ms; display:block;"></span>
                                </span>
                                <input id="is_anonymous" name="is_anonymous" type="checkbox" value="1" style="position: absolute; left:-9999px;" aria-hidden="true">
                            </label>
                        </div>
                        
                        <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1.5rem;">
                            <button type="button" onclick="document.getElementById('donate-modal').style.display='none'" style="padding: 0.875rem 1.75rem; background: white; color: #6b7280; border: 2px solid #e5e7eb; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.9375rem; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb'">
                                Cancel
                            </button>
                            <button type="submit" id="donate-submit-btn" style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); font-size: 0.9375rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke-width="2"/>
                                </svg>
                                Donate
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endauth
@endif

{{-- Comment Modal --}}
@if(!Auth::guard('admin')->check())
    @auth
        <div id="comment-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
            <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2.5rem; max-width: 540px; width: 90%; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.75rem; font-weight: 700; color: #059669; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke-width="2"/>
                            </svg>
                        </div>
                        Post a comment
                    </h3>
                    <button type="button" onclick="document.getElementById('comment-modal').style.display='none'" style="background: #f3f4f6; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#e5e7eb'; this.style.color='#374151'" onmouseout="this.style.background='#f3f4f6'; this.style.color='#6b7280'">&times;</button>
                </div>

                <form method="POST" action="{{ route('api.campaigns.comments.store', $campaign->campaign_id) }}">
                    @csrf

                    <div style="margin-bottom: 1.5rem;">
                        <label for="comment-text" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9375rem;">Comment *</label>
                        <textarea id="comment-text" name="text" rows="5" required maxlength="2000" style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9375rem; font-family: inherit; resize: vertical; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
                    </div>

                    <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1.5rem;">
                        <button type="button" onclick="document.getElementById('comment-modal').style.display='none'" style="padding: 0.875rem 1.75rem; background: white; color: #6b7280; border: 2px solid #e5e7eb; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.9375rem; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb'">
                            Cancel
                        </button>
                        <button type="submit" style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); font-size: 0.9375rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M22 2L11 13" stroke-width="2"/>
                                <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke-width="2"/>
                            </svg>
                            Post comment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth
@endif

{{-- Update Modal --}}
@auth
    @if($isOwnerOrCollaborator)
        <div id="update-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
            <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2.5rem; max-width: 540px; width: 90%; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.75rem; font-weight: 700; color: #059669; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" stroke-width="2"/>
                            </svg>
                        </div>
                        Post an update
                    </h3>
                    <button type="button" onclick="document.getElementById('update-modal').style.display='none'" style="background: #f3f4f6; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#e5e7eb'; this.style.color='#374151'" onmouseout="this.style.background='#f3f4f6'; this.style.color='#6b7280'">&times;</button>
                </div>

                <form method="POST" action="{{ route('campaigns.updates.store', $campaign->campaign_id) }}">
                    @csrf

                    <div style="margin-bottom: 1.5rem;">
                        <label for="update-title" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9375rem;">Title *</label>
                        <input id="update-title" name="title" type="text" maxlength="255" required style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9375rem; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label for="update-content" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9375rem;">Update *</label>
                        <textarea id="update-content" name="content" rows="6" required maxlength="5000" style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9375rem; font-family: inherit; resize: vertical; transition: all 0.2s;" onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
                    </div>

                    <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1.5rem;">
                        <button type="button" onclick="document.getElementById('update-modal').style.display='none'" style="padding: 0.875rem 1.75rem; background: white; color: #6b7280; border: 2px solid #e5e7eb; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.9375rem; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb'">
                            Cancel
                        </button>
                        <button type="submit" style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); font-size: 0.9375rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M22 2L11 13" stroke-width="2"/>
                                <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke-width="2"/>
                            </svg>
                            Post update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endauth

<!-- Share modal (rich) -->
<div id="share-modal" class="share-modal-root" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="share-modal-title" tabindex="-1">
    <div class="share-panel" role="document">
        <button aria-label="Close share" id="share-close-btn" class="share-close">&times;</button>
        <h3 id="share-modal-title" class="share-title">Quick Share</h3>

        <div class="share-link-row">
            <div class="share-link-box">
                <div>
                    <small class="muted">Your exclusive link</small>
                    <div id="share-link-text" class="share-link-text">https://example.com</div>
                </div>
                <button id="share-copy-btn" class="button share-copy">Copy link</button>
            </div>
        </div>

        <p class="share-heading">Share this campaign to reach more donators</p>
        <p class="muted share-sub">Choose an option below to share quickly.</p>

        <div class="share-grid" role="list">
            <a id="share-facebook" class="share-option" role="listitem" target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/facebook.png') }}" alt="" width="18" height="18" /></span>
                <span class="share-label">Facebook</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>

            <a id="share-whatsapp" class="share-option" role="listitem" target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/whatsapp.png') }}" alt="" width="18" height="18" /></span>
                <span class="share-label">WhatsApp</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>

            <a id="share-messenger" class="share-option" role="listitem" target="_blank" rel="noopener noreferrer" aria-label="Share on Messenger">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/messenger.png') }}" alt="" width="18" height="18" /></span>
                <span class="share-label">Messenger</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>

            <a id="share-email" class="share-option" role="listitem" target="_blank" rel="noopener noreferrer" aria-label="Share by email">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/email.png') }}" alt="" width="18" height="18" /></span>
                <span class="share-label">Email</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>

            <a id="share-linkedin" class="share-option" role="listitem" target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/linkedin.png') }}" alt="" width="18" height="18" /></span>
                <span class="share-label">LinkedIn</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>

            <a id="share-instagram" class="share-option" role="listitem" target="_blank" rel="noopener noreferrer" aria-label="Share on Instagram">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/instagram.png') }}" alt="" width="18" height="18" /></span>
                <span class="share-label">Instagram</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>

            <a id="share-x" class="share-option" role="listitem" target="_blank" rel="noopener noreferrer" aria-label="Share on X">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/twitter.png') }}" alt="" width="18" height="18" /></span>
                <span class="share-label">X</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>

            <a id="share-qr" class="share-option" role="listitem" href="#" aria-label="Open QR code">
                <span class="share-icon" aria-hidden="true"><img src="{{ asset('images/ShareImages/qr.png') }}" alt="" /></span>
                <span class="share-label">QR Code</span>
                <span class="share-count" aria-hidden="true"></span>
                <span class="share-spinner" aria-hidden="true"></span>
            </a>
        </div>

        <!-- QR is available as a grid button above; small QR removed to keep consistent grid layout -->

        <!-- QR popup modal -->
        <div id="share-qr-modal" class="qr-modal" role="dialog" aria-modal="true" aria-labelledby="share-qr-modal-title" style="display:none;">
            <div class="qr-modal-panel" role="document">
                <button id="share-qr-close" class="share-close" aria-label="Close QR dialog"><span aria-hidden="true">&times;</span></button>
                <h4 id="share-qr-modal-title" class="muted qr-title">QR code</h4>

                <div class="qr-content">
                    <img id="share-qr-large" src="" alt="Large QR code" class="qr-image" />

                    <div class="qr-actions" aria-hidden="false">
                        <button id="share-qr-download" class="button">Download QR</button>
                        <button id="share-qr-copylink" class="button button-outline">Copy link</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.report_modal')

{{-- Media Lightbox Modal --}}
<div id="media-lightbox" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.95);padding:20px;overflow:auto;">
    <button id="media-lightbox-close" style="position:fixed;top:20px;right:20px;width:44px;height:44px;border-radius:50%;border:none;background:rgba(255,255,255,0.2);color:white;font-size:28px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10000;transition:background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
        &times;
    </button>
    <div id="media-lightbox-content" style="display:flex;align-items:center;justify-content:center;min-height:100%;padding:60px 0;">
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Media lightbox functionality
    var mediaLightbox = document.getElementById('media-lightbox');
    var mediaLightboxContent = document.getElementById('media-lightbox-content');
    var mediaLightboxClose = document.getElementById('media-lightbox-close');
    
    if (mediaLightbox && mediaLightboxContent && mediaLightboxClose) {
        // Add click handlers to all media-open buttons
        document.querySelectorAll('.media-open').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var src = this.getAttribute('data-src');
                var type = this.getAttribute('data-type');
                
                mediaLightboxContent.innerHTML = '';
                
                if (type === 'image') {
                    var img = document.createElement('img');
                    img.src = src;
                    img.style.cssText = 'max-width:100%;max-height:90vh;object-fit:contain;border-radius:8px;box-shadow:0 10px 50px rgba(0,0,0,0.5);';
                    mediaLightboxContent.appendChild(img);
                } else if (type === 'video') {
                    var video = document.createElement('video');
                    video.src = src;
                    video.controls = true;
                    video.autoplay = true;
                    video.style.cssText = 'max-width:100%;max-height:90vh;border-radius:8px;box-shadow:0 10px 50px rgba(0,0,0,0.5);';
                    mediaLightboxContent.appendChild(video);
                } else if (type === 'file') {
                    // Open file in new tab
                    window.open(src, '_blank');
                    return;
                }
                
                mediaLightbox.style.display = 'block';
            });
        });
        
        // Close lightbox
        mediaLightboxClose.addEventListener('click', function() {
            mediaLightbox.style.display = 'none';
            mediaLightboxContent.innerHTML = '';
        });
        
        // Close on background click
        mediaLightbox.addEventListener('click', function(e) {
            if (e.target === mediaLightbox) {
                mediaLightbox.style.display = 'none';
                mediaLightboxContent.innerHTML = '';
            }
        });
        
        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mediaLightbox.style.display === 'block') {
                mediaLightbox.style.display = 'none';
                mediaLightboxContent.innerHTML = '';
            }
        });
    }


    // Media upload functionality
    var addMediaBtn = document.getElementById('add-media-btn');
    var mediaFileInput = document.getElementById('media-file-input');
    var mediaUploadForm = document.getElementById('media-upload-form');
    
    if (addMediaBtn && mediaFileInput && mediaUploadForm) {
        addMediaBtn.addEventListener('click', function() {
            mediaFileInput.click();
        });
        
        mediaFileInput.addEventListener('change', function() {
            if (mediaFileInput.files.length > 0) {
                mediaUploadForm.submit();
            }
        });
    }

    // Share modal behavior: populate modal, copy, QR and social links
    try {
        var shareBtn = document.getElementById('share-btn');
        var shareModal = document.getElementById('share-modal');
        var shareLinkText = document.getElementById('share-link-text');
        var shareCopyBtn = document.getElementById('share-copy-btn');
        // current QR URL for modal (generated when opening share modal)
        var _currentQrUrl = null;
        // small guard to ignore immediate clicks after closing a modal (prevents reopening)
        var _ignoreClicksUntil = 0;
        function ignoreClicksFor(ms){ _ignoreClicksUntil = Date.now() + ms; }
        // suggested filename for QR download (sanitized on server)
        var _qrFilename = '{{ isset($campaign) ? \Illuminate\Support\Str::slug($campaign->title) . "-" . $campaign->campaign_id . "-qr.png" : "campaign-qr.png" }}';

        function encode(s) { return encodeURIComponent(s); }

        function openShareModal() {
            var url = window.location.href;
            var title = document.querySelector('.campaign-title-modern') ? document.querySelector('.campaign-title-modern').innerText.trim() : document.title;
            var text = title + ' — Support this campaign';

            // populate link text
            if (shareLinkText) shareLinkText.textContent = url;

            // QR (using public QR generator) - store URL for QR modal
            _currentQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' + encode(url);

            // social links
            var fb = document.getElementById('share-facebook');
            var wa = document.getElementById('share-whatsapp');
            var msgr = document.getElementById('share-messenger');
            var mail = document.getElementById('share-email');
            var ln = document.getElementById('share-linkedin');
            var ig = document.getElementById('share-instagram');
            var tw = document.getElementById('share-x');

            if (fb) fb.href = 'https://www.facebook.com/sharer.php?u=' + encode(url);
            if (wa) wa.href = 'https://api.whatsapp.com/send?text=' + encode(text + ' ' + url);
            if (msgr) msgr.href = 'fb-messenger://share?link=' + encode(url);
            if (mail) mail.href = 'mailto:?subject=' + encode(title) + '&body=' + encode(text + '\n\n' + url);
            if (ln) ln.href = 'https://www.linkedin.com/shareArticle?mini=true&url=' + encode(url) + '&title=' + encode(title) + '&summary=' + encode(text);
            if (ig) ig.href = 'https://www.instagram.com/';
            if (tw) tw.href = 'https://twitter.com/intent/tweet?text=' + encode(text) + '&url=' + encode(url);

                // show modal - add open class for animations and manage focus
                if (shareModal) {
                    shareModal.style.display = 'block';
                    // small timeout to allow CSS transition
                    setTimeout(function(){ shareModal.classList.add('open'); }, 10);
                    // focus management
                    var closeBtn = document.getElementById('share-close-btn');
                    var firstFocusable = shareCopyBtn || closeBtn;
                    if (firstFocusable) firstFocusable.focus({ preventScroll: true });
                    // trap focus
                    trapFocus(shareModal);
                }
        }

        if (shareBtn) {
            // ripple on pointer down (visual feedback)
            shareBtn.addEventListener('pointerdown', function(e){
                try {
                    var rect = shareBtn.getBoundingClientRect();
                    var ripple = document.createElement('span');
                    ripple.className = 'share-cta__ripple';
                    var size = Math.max(rect.width, rect.height) * 2;
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
                    ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
                    shareBtn.appendChild(ripple);
                    setTimeout(function(){ try{ ripple.remove(); }catch(e){} }, 650);
                } catch (err) { /* ignore */ }
            });

            // keyboard activation ripple for Enter/Space
            shareBtn.addEventListener('keydown', function(e){ if (e.key === 'Enter' || e.key === ' ') {
                try {
                    var rect = shareBtn.getBoundingClientRect();
                    var ripple = document.createElement('span');
                    ripple.className = 'share-cta__ripple';
                    var size = Math.max(rect.width, rect.height) * 2;
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = (rect.width/2 - size/2) + 'px';
                    ripple.style.top = (rect.height/2 - size/2) + 'px';
                    shareBtn.appendChild(ripple);
                    setTimeout(function(){ try{ ripple.remove(); }catch(e){} }, 650);
                } catch (err) {}
            }});

            shareBtn.addEventListener('click', function(e) {
                e.preventDefault();
                openShareModal();
            });
        }

        // copy button
        if (shareCopyBtn) {
            shareCopyBtn.addEventListener('click', function() {
                var u = shareLinkText ? shareLinkText.textContent : window.location.href;
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(u).then(function() { showShareToast('Link copied to clipboard'); }).catch(function() { fallbackCopy(u); });
                } else {
                    fallbackCopy(u);
                }
            });
        }

        // close button
        var shareClose = document.getElementById('share-close-btn');
        if (shareClose) {
            shareClose.addEventListener('click', function(){ closeShareModal(); });
        }

        // close on Escape and allow keyboard navigation
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeShareModal(); });

        function closeShareModal(){
            if (!shareModal) return;
            shareModal.classList.remove('open');
            // delay hiding to allow animation
            setTimeout(function(){ shareModal.style.display = 'none'; }, 260);
            releaseFocusTrap();
            // ignore clicks for a short period to avoid accidental re-open
            try { ignoreClicksFor(420); } catch(e){}
        }

        

        // Instagram: copy link then open Instagram (web fallback)
        try {
            var igBtn = document.getElementById('share-instagram');
            if (igBtn) {
                igBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var u = shareLinkText ? shareLinkText.textContent : window.location.href;
                    var target = igBtn.href || 'https://www.instagram.com/';
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(u).then(function() {
                            showShareToast('Link copied to clipboard — open Instagram and paste');
                            window.open(target, '_blank');
                        }).catch(function() {
                            fallbackCopy(u);
                            window.open(target, '_blank');
                        });
                    } else {
                        fallbackCopy(u);
                        window.open(target, '_blank');
                    }
                });
            }
        } catch (e) { console.error(e); }

        // Add click handlers to show a small loading state on share buttons
        try {
            var shareOptions = document.querySelectorAll('.share-option');
            shareOptions.forEach(function(opt){
                    opt.addEventListener('click', function(ev){
                        try {
                            // ignore clicks when within the ignore window (prevents accidental re-open)
                            if (Date.now() < _ignoreClicksUntil) { ev.stopPropagation(); ev.preventDefault(); return; }
                            // skip QR special-case (QR handled separately)
                            if (opt.id === 'share-qr') return;
                            // if some handler already prevented default (special-case), skip
                            if (ev.defaultPrevented) return;
                            // prevent default so we can show spinner
                            ev.preventDefault();
                            var href = opt.href || opt.getAttribute('data-href');
                            opt.classList.add('loading');
                            // announce loading for screen readers
                            showShareToast('Opening...');
                            setTimeout(function(){
                                // open link in new tab/window
                                if (href) window.open(href, '_blank', 'noopener');
                                opt.classList.remove('loading');
                            }, 700);
                        } catch (e) { console.error(e); }
                    });
                // keyboard: allow Enter/Space to trigger
                opt.addEventListener('keydown', function(e){ if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); opt.click(); } });
            });
        } catch (e) { console.error('share options init failed', e); }

        // populate share-counts if present as data-share-count attributes
        try {
            document.querySelectorAll('.share-option').forEach(function(opt){
                var c = opt.getAttribute('data-share-count');
                var sc = opt.querySelector('.share-count');
                if (c && sc) sc.textContent = c;
            });
        } catch (e) { /* ignore */ }

        function fallbackCopy(u) {
            try {
                var ta = document.createElement('textarea');
                ta.value = u;
                ta.style.position = 'fixed'; ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
                showShareToast('Link copied to clipboard');
            } catch (e) {
                window.prompt('Copy this link', u);
            }
        }

        // show toast (reuse existing) - accessible
        function showShareToast(msg) {
            var t = document.querySelector('.share-toast');
            if (!t) {
                t = document.createElement('div');
                t.className = 'share-toast';
                t.setAttribute('role','status');
                t.setAttribute('aria-live','polite');
                t.setAttribute('aria-atomic','true');
                document.body.appendChild(t);
            }
            t.textContent = msg; t.classList.add('visible'); clearTimeout(t._hideTimer); t._hideTimer = setTimeout(function() { t.classList.remove('visible'); }, 2600);
        }

        // Close modal when clicking outside inner panel
        if (shareModal) {
            shareModal.addEventListener('click', function(e) {
                if (e.target === shareModal) { e.stopPropagation(); e.preventDefault(); closeShareModal(); }
            });
        }

        // QR popup behavior
        try {
            var qrBtn = document.getElementById('share-qr');
            var qrModal = document.getElementById('share-qr-modal');
            var qrLarge = document.getElementById('share-qr-large');
            var qrClose = document.getElementById('share-qr-close');
            var qrDownload = document.getElementById('share-qr-download');
            var qrCopylink = document.getElementById('share-qr-copylink');

            function openQrModal(){
                if (!qrModal) return;
                // ensure large image uses generated QR URL if available
                if (qrLarge) qrLarge.src = _currentQrUrl || ('https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' + encode(window.location.href));
                qrModal.style.display = 'flex';
                setTimeout(function(){ qrModal.classList.add('open'); }, 8);
                // focus
                if (qrClose) qrClose.focus();
                trapFocus(qrModal);
            }

            function closeQrModal(){
                if (!qrModal) return;
                qrModal.classList.remove('open');
                setTimeout(function(){ qrModal.style.display = 'none'; }, 200);
                releaseFocusTrap();
                try { ignoreClicksFor(420); } catch(e){}
            }

            if (qrBtn) qrBtn.addEventListener('click', function(e){
                try {
                    if (Date.now() < _ignoreClicksUntil) { e.stopPropagation(); e.preventDefault(); return; }
                } catch (err) {}
                e.preventDefault(); openQrModal();
            });
            if (qrClose) qrClose.addEventListener('click', function(){ closeQrModal(); });
                if (qrModal) qrModal.addEventListener('click', function(e){ if (e.target === qrModal) { e.stopPropagation(); closeQrModal(); } });
            document.addEventListener('keydown', function(e){ if (e.key === 'Escape') { if (qrModal && qrModal.style.display !== 'none') closeQrModal(); } });

            if (qrDownload) {
                qrDownload.addEventListener('click', function(){
                    try {
                        var src = qrLarge.src || _currentQrUrl || ('https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' + encode(window.location.href));
                        // Try fetching the image as a blob first so we can trigger a proper download
                        fetch(src).then(function(resp){
                            if (!resp.ok) throw new Error('Network response was not ok');
                            return resp.blob();
                        }).then(function(blob){
                            var url = URL.createObjectURL(blob);
                            var a = document.createElement('a');
                            a.href = url;
                            a.download = _qrFilename || 'campaign-qr.png';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            // revoke the object URL shortly after
                            setTimeout(function(){ URL.revokeObjectURL(url); }, 1500);
                        }).catch(function(err){
                            // If fetch fails (CORS or network), fallback to opening the image URL directly
                            console.error('QR download failed via fetch, falling back:', err);
                            try {
                                var a2 = document.createElement('a');
                                a2.href = src;
                                a2.download = _qrFilename || 'campaign-qr.png';
                                document.body.appendChild(a2);
                                a2.click();
                                document.body.removeChild(a2);
                            } catch (e2) { console.error('Fallback download also failed', e2); }
                        });
                    } catch (e) { console.error(e); }
                });
            }

            if (qrCopylink) {
                qrCopylink.addEventListener('click', function(){
                    var u = shareLinkText ? shareLinkText.textContent : window.location.href;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(u).then(function(){ showShareToast('Link copied to clipboard'); });
                    } else { fallbackCopy(u); }
                });
            }
            // ensure QR close also ignores immediate clicks
            if (qrClose) {
                var _origQrClose = qrClose.onclick;
                // when using the dedicated close handler, ensure ignore window
                qrClose.addEventListener('click', function(){ try { ignoreClicksFor(420); } catch(e){} });
            }
        } catch (e) { console.error('QR init failed', e); }

        // Focus trap implementation (simple, works for modal)
        var _previouslyFocused = null;
        var _trapHandler = null;
        function trapFocus(modal) {
            _previouslyFocused = document.activeElement;
            var focusable = modal.querySelectorAll('a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
            focusable = Array.prototype.filter.call(focusable, function(el){ return !el.hasAttribute('disabled') && el.offsetParent !== null; });
            var first = focusable[0];
            var last = focusable[focusable.length-1];
            _trapHandler = function(e){
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === first) { e.preventDefault(); last.focus(); }
                    } else {
                        if (document.activeElement === last) { e.preventDefault(); first.focus(); }
                    }
                }
            };
            document.addEventListener('keydown', _trapHandler);
        }
        function releaseFocusTrap(){ if (_trapHandler) document.removeEventListener('keydown', _trapHandler); if (_previouslyFocused) try{ _previouslyFocused.focus(); }catch(e){} }

    } catch (e) {
        console.error('Share modal init error', e);
    }

    // Anonymous contribution toggle functionality
    var anonymousCheckbox = document.getElementById('is_anonymous');
    var anonymousLabel = document.querySelector('label[for="is_anonymous"]');
    
    if (anonymousCheckbox && anonymousLabel) {
        var switchTrack = anonymousLabel.querySelector('.switch-track');
        var switchThumb = anonymousLabel.querySelector('.switch-thumb');
        
        function updateToggleUI() {
            if (anonymousCheckbox.checked) {
                if (switchTrack) switchTrack.style.background = '#1aa37a';
                if (switchThumb) switchThumb.style.left = '21px';
            } else {
                if (switchTrack) switchTrack.style.background = '#e6e6e6';
                if (switchThumb) switchThumb.style.left = '3px';
            }
        }
        
        // Handle label click (which naturally toggles the checkbox)
        anonymousLabel.addEventListener('click', function(e) {
            // Let the default checkbox toggle happen, then update UI
            setTimeout(updateToggleUI, 0);
        });
        
        // Handle direct checkbox change (keyboard users)
        anonymousCheckbox.addEventListener('change', updateToggleUI);
        
        // Initialize UI on page load
        updateToggleUI();
    }

    // Close modals on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var donateModal = document.getElementById('donate-modal');
            if (donateModal && donateModal.style.display !== 'none') donateModal.style.display = 'none';
            var commentModal = document.getElementById('comment-modal');
            if (commentModal && commentModal.style.display !== 'none') commentModal.style.display = 'none';
            var updateModal = document.getElementById('update-modal');
            if (updateModal && updateModal.style.display !== 'none') updateModal.style.display = 'none';
        }
    });

    // Donation confetti animation
    var donateForm = document.getElementById('donate-form');
    if (donateForm) {
        donateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var submitBtn = document.getElementById('donate-submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10" stroke-width="4" stroke-dasharray="31.4 31.4" stroke-linecap="round"/></svg> Processing...';
            }
            
            // Create confetti animation
            createConfetti();
            
            // Submit after animation starts
            setTimeout(function() {
                donateForm.submit();
            }, 500);
        });
    }
    
    function createConfetti() {
        var colors = ['#10b981', '#059669', '#34d399'];
        var billCount = 50;
        var container = document.createElement('div');
        container.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;';
        document.body.appendChild(container);
        
        for (var i = 0; i < billCount; i++) {
            createMoneyBill(container, colors);
        }
        
        setTimeout(function() {
            document.body.removeChild(container);
        }, 5000);
    }
    
    function createMoneyBill(container, colors) {
        var bill = document.createElement('div');
        var color = colors[Math.floor(Math.random() * colors.length)];
        var width = 50 + Math.random() * 20;
        var height = width * 0.45;
        var left = Math.random() * 100;
        var delay = Math.random() * 0.8;
        var duration = 3 + Math.random() * 2;
        var rotation = Math.random() * 360;
        
        // Create money bill with euro symbol
        bill.style.cssText = 'position: absolute; width: ' + width + 'px; height: ' + height + 'px; background: linear-gradient(135deg, ' + color + ' 0%, ' + adjustColor(color, -20) + ' 100%); left: ' + left + '%; top: -100px; border-radius: 3px; opacity: 0.95; transform: rotate(' + rotation + 'deg); box-shadow: 0 2px 4px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; font-weight: bold; color: rgba(255,255,255,0.8); font-size: ' + (height * 0.6) + 'px; border: 2px solid rgba(255,255,255,0.3);';
        bill.textContent = '€';
        
        container.appendChild(bill);
        
        var xMovement = (Math.random() - 0.5) * 300;
        var rotationEnd = rotation + (Math.random() - 0.5) * 720;
        
        bill.animate([
            { transform: 'translateY(0) translateX(0) rotateX(0deg) rotate(' + rotation + 'deg)', opacity: 0.95 },
            { transform: 'translateY(' + (window.innerHeight + 100) + 'px) translateX(' + xMovement + 'px) rotateX(' + (Math.random() * 360) + 'deg) rotate(' + rotationEnd + 'deg)', opacity: 0.7 }
        ], {
            duration: duration * 1000,
            delay: delay * 1000,
            easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
            fill: 'forwards'
        });
    }
    
    function adjustColor(color, amount) {
        var num = parseInt(color.replace('#', ''), 16);
        var r = Math.max(0, Math.min(255, (num >> 16) + amount));
        var g = Math.max(0, Math.min(255, ((num >> 8) & 0x00FF) + amount));
        var b = Math.max(0, Math.min(255, (num & 0x0000FF) + amount));
        return '#' + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
    }

    // Notification highlight functionality
    function highlightNotificationTarget() {
        var urlParams = new URLSearchParams(window.location.search);
        var hash = window.location.hash;
        
        // Check for notification parameters
        var donationId = urlParams.get('donation');
        var commentId = urlParams.get('comment');
        var updateId = urlParams.get('update');
        
        var targetElement = null;
        
        // Find the target element based on URL parameters or hash
        if (donationId) {
            targetElement = document.querySelector('[data-donation-id="' + donationId + '"]');
        } else if (commentId) {
            targetElement = document.querySelector('[data-comment-id="' + commentId + '"]');
        } else if (updateId) {
            targetElement = document.querySelector('[data-update-id="' + updateId + '"]');
        } else if (hash) {
            // Also support hash-based targeting
            targetElement = document.querySelector(hash);
        }
        
        if (targetElement) {
            // Scroll to element with smooth behavior
            setTimeout(function() {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Add highlight class
                targetElement.classList.add('notification-highlight');
                
                // Remove highlight after animation
                setTimeout(function() {
                    targetElement.classList.remove('notification-highlight');
                }, 3000);
            }, 300);
        }
    }
    
    // Run highlight on page load
    highlightNotificationTarget();
});
</script>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Notification highlight animation */
@keyframes highlightPulse {
    0%, 100% { 
        background-color: rgba(16, 185, 129, 0.15);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
    }
    50% { 
        background-color: rgba(16, 185, 129, 0.25);
        box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
    }
}

.notification-highlight {
    animation: highlightPulse 1.5s ease-in-out 2;
    border-radius: 8px;
    transition: all 0.3s ease;
}

/* Small toast for share/copy feedback */
.share-toast {
    position: fixed;
    right: 1rem;
    bottom: 1.2rem;
    background: rgba(0,0,0,0.88);
    color: #fff;
    padding: 0.65rem 1rem;
    border-radius: 8px;
    font-size: 0.95rem;
    z-index: 1200;
    box-shadow: 0 8px 28px rgba(0,0,0,0.22);
    opacity: 0;
    transform: translateY(10px) scale(0.98);
    transition: opacity 220ms cubic-bezier(.2,.9,.2,1), transform 220ms cubic-bezier(.2,.9,.2,1);
}
.share-toast.visible { opacity: 1; transform: translateY(0) scale(1); }
.share-toast[role="status"] { outline: none; }

/* Share modal root overlay */
.share-modal-root {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1400;
    background: rgba(0,0,0,0.45);
    overflow: auto;
    padding: 2.5rem 1rem;
}

.share-panel {
    background: #fff;
    max-width: 820px;
    margin: 0 auto;
    border-radius: 8px;
    padding: 1.25rem 1rem;
    position: relative;
}

/* Close button styling (share + QR use the same polished control) */
.share-close {
    position: absolute;
    right: 0.6rem;
    top: 0.6rem;
    background: #ffffff;
    border: 1px solid rgba(15,23,42,0.06);
    padding: 0;
    margin: 0;
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 18px;
    line-height: 1;
    color: #374151;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(2,6,23,0.08);
    transition: transform 150ms ease, background 150ms ease, color 150ms ease;
    z-index: 1202;
    -webkit-appearance: none;
    appearance: none;
}
.share-close span { display:block; transform: translateY(-1px); }
.share-close:hover { transform: scale(1.06); color: #111827; background: #f8fafc; }
.share-close:focus { outline: 3px solid rgba(59,130,246,0.18); outline-offset: 3px; }

.share-title { margin: 0 0 0.5rem 0; font-size: 1.25rem; }

.share-link-row { display:flex; gap:0.6rem; align-items:center; margin-bottom:0.8rem; padding-right:3rem; box-sizing:border-box; }
.share-link-box { flex:1; background:#f6f6f6; padding:0.6rem 0.8rem; border-radius:6px; display:flex; align-items:center; justify-content:space-between; gap:0.6rem; }
.share-link-text { font-size:0.95rem; word-break:break-all; overflow:hidden; text-overflow:ellipsis; }
.share-copy { white-space:nowrap; }

.share-heading { margin:0 0 0.2rem 0; font-weight:600; }
.share-sub { margin-top:0; margin-bottom:0.9rem; }

/* Theme variables and sizing (can be adapted to your app variables) */
:root {
    --bg-start: #f7fbff;
    --bg-end: #f3f6ff;
    --accent: #3b82f6; /* blue-500 */
    --text: #0f172a; /* slate-900 */
    --muted: #6b7280; /* gray-500 */
    --icon-size: 36px; /* target icon image size (32-40px) */
    --icon-box: 64px;  /* circular container size */
    --option-gap: 1rem;
    --option-padding: 0.85rem;
}

/* Grid of share options */
.share-grid { display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px 18px; }
.share-option { display:flex; align-items:center; gap:var(--option-gap); padding:var(--option-padding); border-radius:12px; text-decoration:none; color:var(--text); border:1px solid rgba(25,25,25,0.04); background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(250,250,252,0.98)); box-shadow: 0 2px 6px rgba(16,24,40,0.04); transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease; min-height: calc(var(--icon-box) + 8px); }
.share-option:focus, .share-option:hover { transform: translateY(-4px); box-shadow: 0 10px 30px rgba(16,24,40,0.08); }
.share-icon { display:inline-flex; align-items:center; justify-content:center; width:var(--icon-box); height:var(--icon-box); border-radius:12px; background: linear-gradient(180deg, rgba(250,250,255,1), rgba(243,246,255,1)); flex:0 0 var(--icon-box); transition: transform 220ms cubic-bezier(.2,.9,.2,1); }
.share-icon img { width:var(--icon-size); height:var(--icon-size); display:block; object-fit:contain; margin:auto; }
.share-option:hover .share-icon, .share-option:focus .share-icon { transform: translateY(-6px) rotate(-6deg) scale(1.03); }
.share-label { font-size:1rem; font-weight:600; color:var(--text); }
.share-count { margin-left:auto; font-size:0.85rem; color:var(--muted); }
.share-spinner { width:18px; height:18px; margin-left:0.4rem; display:none; }
.share-option.loading { opacity:0.9; pointer-events:none; }
.share-option.loading .share-spinner { display:inline-block; }
.share-spinner::after { content: ''; display:block; width:100%; height:100%; border-radius:50%; border:2px solid rgba(0,0,0,0.12); border-top-color: var(--accent); animation: spinner 900ms linear infinite; }

@keyframes spinner { to { transform: rotate(360deg); } }

.share-bottom { display:flex; gap:1rem; margin-top:1rem; align-items:center; }

@media (min-width: 900px) {
    .share-grid { grid-template-columns: repeat(4, minmax(0,1fr)); }
}

@media (max-width: 520px) {
    .share-panel { padding:1rem; }
    .share-grid { grid-template-columns: repeat(2, 1fr); }
    .share-icon { width:48px; height:48px; }
    .share-icon img { width:28px; height:28px; }
}

/* subtle gradient background for panel */
.share-panel { background-image: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(250,252,255,0.98)); }

/* fade-in for modal & elements */
.share-panel, .share-option, .share-link-box { opacity: 0; transform: translateY(8px); transition: opacity 320ms ease, transform 320ms ease; }
.share-modal-root.open .share-panel, .share-modal-root.open .share-option, .share-modal-root.open .share-link-box { opacity: 1; transform: translateY(0); }

/* focus visible outlines for keyboard users */
.share-option:focus-visible, .share-copy:focus-visible, #share-copy-btn:focus-visible, #share-close-btn:focus-visible { outline: 3px solid rgba(59,130,246,0.22); outline-offset: 3px; }

/* QR modal styles */
.qr-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display:flex; align-items:center; justify-content:center; z-index:1500; padding: 1.2rem; }

/* panel appearance and entrance animation */
.qr-modal-panel {
    position: relative;
    background: #ffffff;
    padding: 1.25rem 1.25rem 1.4rem;
    border-radius: 14px;
    box-shadow: 0 18px 50px rgba(2,6,23,0.28);
    max-width: 520px;
    width: 100%;
    box-sizing: border-box;
    opacity: 0;
    transform: translateY(10px) scale(0.995);
    transition: opacity 260ms cubic-bezier(.2,.9,.2,1), transform 260ms cubic-bezier(.2,.9,.2,1);
}
.qr-modal.open .qr-modal-panel { opacity: 1; transform: translateY(0) scale(1); }

/* Close button specific to QR modal (override share-close inside qr-modal) */
.qr-modal .share-close {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #ffffff;
    border: 1px solid rgba(15,23,42,0.06);
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 18px;
    line-height: 1;
    color: #374151;
    cursor: pointer;
    box-shadow: 0 6px 18px rgba(2,6,23,0.08);
    transition: transform 150ms ease, background 150ms ease, color 150ms ease;
    z-index: 1601;
    padding: 0;
}
.qr-modal .share-close span { display:block; transform: translateY(-1px); }
.qr-modal .share-close:hover { transform: scale(1.06); color: #111827; background: #f8fafc; }
.qr-modal .share-close:focus { outline: 3px solid rgba(59,130,246,0.18); outline-offset: 3px; }

.qr-title { margin: 0 0 0.6rem 0; text-align: center; font-weight: 600; }

.qr-content { display:flex; gap:1rem; align-items:center; flex-direction:column; justify-content:center; }
.qr-image { width: 260px; height: 260px; border-radius: 12px; border: 1px solid #eee; background: #fff; object-fit: contain; }

.qr-actions { display:flex; gap:0.75rem; align-items:center; justify-content:center; margin-top: 0.6rem; }
.qr-actions .button { min-width: 120px; }

@media (max-width: 520px) {
    .qr-image { width: 220px; height: 220px; }
    .qr-modal-panel { padding: 1rem; border-radius: 12px; }
    .qr-actions .button { min-width: 100px; font-size: 0.95rem; }
}

/* Creator Controls */
.creator-controls {
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-creator-action,
.btn-creator-delete {
    width: 100%;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-creator-action {
    background: white;
    border: 2px solid #0d7db8;
    color: #0d7db8;
}

.btn-creator-action:hover {
    background: #f0f9ff;
    transform: translateY(-1px);
}

.btn-creator-delete {
    background: white;
    border: 2px solid #ef4444;
    color: #ef4444;
}

.btn-creator-delete:hover {
    background: #fef2f2;
    transform: translateY(-1px);
}

/* Admin Actions */
.admin-actions {
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.btn-admin,
.btn-admin-danger {
    width: 100%;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.btn-admin {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-admin:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-admin-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-admin-danger:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

/* Report Button */
.btn-report-campaign {
    width: 100%;
    padding: 10px 16px;
    background: white;
    border: 2px solid #f59e0b;
    border-radius: 10px;
    color: #f59e0b;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 12px;
}

.btn-report-campaign:hover {
    background: #fffbeb;
    border-color: #d97706;
    color: #d97706;
}
</style>

<style>
/* Modern Campaign Page Styles */
.campaign-page-modern {
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafb 0%, #ffffff 100%);
}

.campaign-hero-section {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    margin-bottom: -80px;
}

.hero-image-container {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    position: relative;
}

.hero-image-container::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.6) 100%);
}

.status-badge {
    position: absolute;
    top: 24px;
    right: 24px;
    padding: 8px 16px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 10;
}

.status-badge.status-completed {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.status-badge.status-suspended {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.campaign-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px 80px;
}

.campaign-content-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 32px;
    position: relative;
    z-index: 1;
}

.campaign-main-col {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.campaign-header-modern {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(26, 163, 122, 0.1);
}

.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%);
    color: #0369a1;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 16px;
}

.campaign-title-modern {
    font-size: 3rem;
    font-weight: 800;
    line-height: 1.2;
    color: #0f172a;
    margin: 0 0 24px;
    letter-spacing: -0.02em;
}

.creator-info-modern {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e2e8f0;
}

.creator-link-modern {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s;
}

.creator-link-modern:hover {
    color: #0d7db8;
}

.creator-link-modern img {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e0f2fe;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.creator-link-modern div {
    display: flex;
    flex-direction: column;
}

.creator-label-small {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.creator-link-modern strong {
    font-size: 1.125rem;
    font-weight: 600;
    color: #0f172a;
}

.about-section-modern,
.media-section-modern,
.updates-section-modern,
.comments-section-modern {
    background: white;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.section-title-modern {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 20px;
}

.section-title-modern svg {
    stroke: #0d7db8;
}

.section-header-flex {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.campaign-description-modern {
    font-size: 1.0625rem;
    line-height: 1.8;
    color: #475569;
    margin: 0;
}

.media-count-badge {
    padding: 4px 12px;
    background: #f1f5f9;
    border-radius: 999px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
}

.media-grid-modern {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}

.updates-list-modern,
.comments-list-modern {
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: 600px;
    overflow-y: auto;
}

.empty-state-text {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
    font-size: 0.9375rem;
}

.btn-icon-action {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    background: white;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    padding: 0;
    flex-shrink: 0;
}

.btn-icon-action svg {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    fill: none;
}

.btn-icon-action:hover {
    border-color: #0d7db8;
    color: #0d7db8;
    background: #f0f9ff;
    transform: translateY(-2px);
}

/* Sidebar Styles */
.campaign-sidebar-modern {
    position: sticky;
    top: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-self: flex-start;
}

.sidebar-card {
    background: white;
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(226, 232, 240, 0.8);
}

.actions-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

.btn-donate-modern {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 1.125rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 10px 30px rgba(16, 185, 129, 0.25);
    margin-bottom: 12px;
}

.btn-donate-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(16, 185, 129, 0.35);
}

.action-buttons-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}

.btn-action-secondary {
    padding: 10px 16px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    color: #64748b;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}

.btn-action-secondary .share-cta__ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(13, 125, 184, 0.3);
    pointer-events: none;
    transform: scale(0);
    animation: ripple-animation 0.6s ease-out;
}

@keyframes ripple-animation {
    to {
        transform: scale(1);
        opacity: 0;
    }
}

.btn-action-secondary:hover {
    border-color: #0d7db8;
    color: #0d7db8;
    background: #f0f9ff;
}

/* Bell wiggle animation on hover for follow button */
.btn-action-secondary img {
    transition: transform 380ms cubic-bezier(.2,.9,.2,1);
}

.btn-action-secondary:hover img {
    transform: rotate(-15deg);
    animation: bell-wiggle-follow 420ms ease;
}

@keyframes bell-wiggle-follow {
    0% { transform: rotate(0deg); }
    30% { transform: rotate(-15deg); }
    60% { transform: rotate(8deg); }
    100% { transform: rotate(0deg); }
}

.btn-action-secondary.following {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border-color: #10b981;
    color: #059669;
}

.creator-badge-sidebar {
    margin-top: 12px;
    padding: 10px 16px;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.progress-card {
    background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
    color: white;
}

.progress-amount {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 16px;
}

.amount-raised {
    font-size: 2.5rem;
    font-weight: 800;
    line-height: 1;
}

.amount-goal {
    font-size: 0.9375rem;
    opacity: 0.9;
}

.progress-bar-modern {
    height: 12px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 999px;
    overflow: hidden;
    margin-bottom: 20px;
}

.progress-fill-modern {
    height: 100%;
    background: white;
    border-radius: 999px;
    transition: width 1s ease;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
}

.stats-grid-modern {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.stat-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 12px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.stat-item svg {
    stroke: white;
    opacity: 0.9;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
}

.stat-label {
    font-size: 0.75rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.sidebar-card-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.125rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 16px;
}

.sidebar-card-title svg {
    stroke: #0d7db8;
}

.donations-list-modern {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 300px;
    overflow-y: auto;
}

.empty-state-text-small {
    text-align: center;
    padding: 20px;
    color: #94a3b8;
    font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .campaign-content-grid {
        grid-template-columns: 1fr 350px;
    }
}

@media (max-width: 968px) {
    .campaign-content-grid {
        grid-template-columns: 1fr;
    }
    
    .campaign-sidebar-modern {
        position: static;
    }
    
    .campaign-hero-section {
        height: 300px;
        margin-bottom: -60px;
    }
    
    .campaign-title-modern {
        font-size: 2rem;
    }
}

@media (max-width: 640px) {
    .campaign-header-modern,
    .about-section-modern,
    .media-section-modern,
    .updates-section-modern,
    .comments-section-modern,
    .sidebar-card {
        padding: 20px;
        border-radius: 16px;
    }
    
    .campaign-title-modern {
        font-size: 1.75rem;
    }
    
    .action-buttons-row {
        grid-template-columns: 1fr;
    }
    
    .media-grid-modern {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
}
</style>

{{-- Collaboration Request Modal --}}
@auth
@if(!Auth::guard('admin')->check() && Auth::id() !== $campaign->creator_id && $campaign->status === 'active' && $campaign->collaborators->count() < 5 && !$hasPendingRequest && !$campaign->collaborators->contains('user_id', Auth::id()))
<div id="collab-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="position: relative; background: white; padding: 2.5rem; max-width: 540px; width: 90%; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e5e7eb;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.75rem; font-weight: 700; color: #059669; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke-width="2"/>
                        <circle cx="8.5" cy="7" r="4" stroke-width="2"/>
                        <line x1="20" y1="8" x2="20" y2="14" stroke-width="2"/>
                        <line x1="23" y1="11" x2="17" y2="11" stroke-width="2"/>
                    </svg>
                </div>
                Request to Collaborate
            </h3>
            <button type="button" id="close-collab-modal" style="background: #f3f4f6; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 8px; transition: all 0.2s;" onmouseover="this.style.background='#e5e7eb'; this.style.color='#374151'" onmouseout="this.style.background='#f3f4f6'; this.style.color='#6b7280'">&times;</button>
        </div>

        <p style="color: #6b7280; margin-bottom: 1.5rem; line-height: 1.6; font-size: 0.9375rem;">
            Send a request to collaborate on <strong style="color: #059669;">{{ $campaign->title }}</strong>. Include a message explaining why you'd like to collaborate.
        </p>
        
        <form method="POST" action="{{ route('collaboration.request', $campaign->campaign_id) }}">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label for="collab-message" style="display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9375rem;">
                    Message (Optional)
                </label>
                <textarea id="collab-message" name="message" rows="4" placeholder="Tell the campaign creator why you'd like to collaborate..." style="width: 100%; padding: 0.875rem; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 0.9375rem; font-family: inherit; resize: vertical; transition: all 0.2s;" maxlength="200" onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
                <span style="font-size: 0.8125rem; color: #9ca3af; margin-top: 0.25rem; display: block;">Maximum 500 characters</span>
            </div>
            
            <div style="display: flex; gap: 0.75rem; justify-content: center; margin-top: 1.5rem;">
                <button type="button" id="cancel-collab-btn" style="padding: 0.875rem 1.75rem; background: white; color: #6b7280; border: 2px solid #e5e7eb; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.9375rem; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='#f9fafb'; this.style.borderColor='#d1d5db'" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb'">
                    Cancel
                </button>
                <button type="submit" style="padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); font-size: 0.9375rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(16, 185, 129, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M22 2L11 13" stroke-width="2"/>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke-width="2"/>
                    </svg>
                    Send Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestBtn = document.getElementById('request-collab-btn');
    const modal = document.getElementById('collab-modal');
    const closeBtn = document.getElementById('close-collab-modal');
    const cancelBtn = document.getElementById('cancel-collab-btn');

    if (requestBtn && modal) {
        requestBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }
});
</script>
@endif
@endauth

@php 
    $isCover = $media->campaign && $media->campaign->cover_media_id === $media->media_id;
    $isOwnerOrCollaborator = Auth::check() && (Auth::id() === $media->campaign->creator_id || $media->campaign->collaborators->contains('user_id', Auth::id()));
@endphp
<div class="media-item" data-media-id="{{ $media->media_id }}" data-media-type="{{ $media->media_type }}" style="position:relative;">
    @if($isCover)
        <span class="cover-pill" style="position:absolute;top:8px;left:8px;z-index:10;background:rgba(15,118,110,0.92);color:white;padding:4px 10px;border-radius:999px;font-size:0.75rem;font-weight:600;">Cover</span>
    @endif

    @if(Auth::guard('admin')->check() || $isOwnerOrCollaborator)
        <form method="POST" action="{{ route('campaigns.media.destroy', ['campaign' => $media->campaign_id, 'media' => $media->media_id]) }}" style="position:absolute; top:8px; right:8px; z-index:10;" onsubmit="return confirm('Are you sure you want to remove this media item?');">
            @csrf
            @method('DELETE')
            <button type="submit" title="Remove media" aria-label="Remove media" style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:9999px;background:#ef4444;border:none;color:white;cursor:pointer;padding:0;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M18 6L6 18M6 6l12 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </form>

        @if($media->media_type === 'image' && !Auth::guard('admin')->check())
            <form method="POST" action="{{ route('campaigns.cover.set', ['campaign' => $media->campaign_id, 'media' => $media->media_id]) }}" style="position:absolute; bottom:8px; right:8px; z-index:10;">
                @csrf
                @method('PATCH')
                <button type="submit" title="Set as cover" aria-label="Set as cover" style="display:inline-flex;align-items:center;gap:0.35rem;padding:6px 10px;border-radius:999px;border:1px solid #0f766e;background:{{ $isCover ? '#0f766e' : 'white' }};color:{{ $isCover ? 'white' : '#0f766e' }};cursor:{{ $isCover ? 'default' : 'pointer' }};font-size:0.85rem;font-weight:600;" {{ $isCover ? 'disabled' : '' }}>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M5 12.5l4 4 10-10" stroke="{{ $isCover ? '#ffffff' : '#0f766e' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    {{ $isCover ? 'Cover' : 'Set as cover' }}
                </button>
            </form>
        @endif
    @endif
    @if($media->media_type === 'image')
        <button type="button" class="media-open" data-src="{{ $media->file_path }}" data-type="image" style="all:unset;cursor:pointer;display:block;">
            <img src="{{ $media->file_path }}" alt="Campaign media" class="media-thumbnail" loading="lazy" style="display:block;max-width:100%;height:auto;border-radius:6px;" />
        </button>
        <small class="media-date">{{ $media->uploaded_at?->format('d M Y') ?? '—' }}</small>
    @elseif($media->media_type === 'video')
        <button type="button" class="media-open" data-src="{{ $media->file_path }}" data-type="video" style="all:unset;cursor:pointer;display:block;">
            <video class="media-thumbnail" controls style="display:block;max-width:100%;height:auto;border-radius:6px;">
                <source src="{{ $media->file_path }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </button>
        <small class="media-date">{{ $media->uploaded_at?->format('d M Y') ?? '—' }}</small>
    @else
        <button type="button" class="media-open" data-src="{{ $media->file_path }}" data-type="file" style="all:unset;cursor:pointer;display:block;">
            <div class="media-file" style="display:flex;align-items:center;gap:0.5rem;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <polyline points="13 2 13 9 20 9" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
                <span class="file-name">{{ basename($media->file_path) }}</span>
            </div>
        </button>
        <small class="media-date">{{ $media->uploaded_at?->format('d M Y') ?? '—' }}</small>
    @endif
</div>

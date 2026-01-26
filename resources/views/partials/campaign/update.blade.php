<li id="update-{{ $update->update_id }}" class="campaign-update">
    <div class="update-header">
        <div>
            <strong>{{ $update->title }}</strong>
            @if($update->author)
                <div style="color: #666; font-size: 0.85rem; margin-top: 4px;">
                    by {{ $update->author->name }}
                </div>
            @endif
        </div>
        <time datetime="{{ $update->date?->toIso8601String() }}" style="color: #777; font-size: 0.88rem; white-space: nowrap;">
            {{ $update->date?->format('d M Y') ?? '—' }}
        </time>
    </div>
    <div class="update-body">
        <p style="overflow-wrap: break-word; word-break: break-word; white-space: normal; max-width:100%;">{{ \Illuminate\Support\Str::limit($update->content, 500) }}</p>
    </div>
</li>


<li id="comment-{{ $comment->comment_id }}" class="campaign-comment">
    <div class="comment-inner">
        <div class="comment-header-row">
            <div class="comment-avatar">
                @if($comment->user)
                    <a href="{{ route('users.show', $comment->user->user_id) }}">
                        <img src="{{ $comment->user->profile_image ?? asset('images/defaultpfp.svg') }}" alt="{{ $comment->user->name }} avatar" loading="lazy">
                    </a>
                @else
                    <img src="{{ asset('images/defaultpfp.svg') }}" alt="Former user avatar" loading="lazy">
                @endif
            </div>

            <div class="author-info">
                @if($comment->user)
                    <a class="author-link" href="{{ route('users.show', $comment->user->user_id) }}"><strong>{{ $comment->user->name }}</strong></a>
                @else
                    <strong>Former user</strong>
                @endif
                <time class="comment-time" datetime="{{ $comment->date?->toIso8601String() }}">{{ $comment->date?->diffForHumans() ?? '—' }}</time>
            </div>

            <div class="comment-controls">
                @if(Auth::guard('admin')->check())
                    <form method="POST" action="{{ route('admin.comments.destroy', $comment->comment_id) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="Delete comment" aria-label="Delete comment" class="delete-btn" data-tooltip="Delete comment" onclick="return confirm('Delete this comment? This action cannot be undone.');">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#f53003" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path></svg>
                        </button>
                    </form>
                @else
                    @auth
                        @if(Auth::id() === $comment->user_id)
                            <form method="POST" action="{{ route('comments.destroy', $comment->comment_id) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete your comment" aria-label="Delete your comment" class="delete-btn" data-tooltip="Delete your comment" onclick="return confirm('Delete your comment? This action cannot be undone.');">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#f53003" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"></path></svg>
                                </button>
                            </form>
                        @else
                            <button type="button" class="report-btn" data-target-type="comment" data-target-id="{{ $comment->comment_id }}" title="Report comment" aria-label="Report comment">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" stroke="currentColor" stroke-width="2"/>
                                    <line x1="4" y1="22" x2="4" y2="15" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                        @endif
                    @endauth
                @endif
            </div>
        </div>

        <div class="comment-body">
            <p>{{ $comment->text }}</p>
        </div>
    </div>
</li>


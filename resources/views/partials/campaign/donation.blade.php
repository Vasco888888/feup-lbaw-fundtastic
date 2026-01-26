<li id="donation-{{ $donation->donation_id }}" class="campaign-donation">
    <div class="donation-inner">
        <div class="donation-header-row">
            <div class="donation-avatar">
                @if($donation->is_anonymous || empty($donation->donator))
                    <img src="{{ asset('images/defaultpfp.svg') }}" alt="Anonymous" loading="lazy">
                @else
                    <a href="{{ route('users.show', $donation->donator->user_id) }}">
                        <img src="{{ $donation->donator->profile_image ?? asset('images/defaultpfp.svg') }}" alt="{{ $donation->donator->name }} avatar" loading="lazy">
                    </a>
                @endif
            </div>

            <div class="donation-author-info">
                @if($donation->is_anonymous)
                    <strong>Anonymous</strong>
                @else
                    @if($donation->donator)
                        <a href="{{ route('users.show', $donation->donator->user_id) }}" class="donor-name">{{ $donation->donator->name }}</a>
                    @else
                        <strong>Former user</strong>
                    @endif
                @endif
                <time class="donation-time" datetime="{{ $donation->date?->toIso8601String() }}">{{ $donation->date?->format('d M Y · H:i') ?? '—' }}</time>
            </div>

            <div class="donation-amount-block">
                <div class="donation-amount">€{{ number_format((float) $donation->amount, 2, '.', ' ') }}</div>
            </div>
        </div>

        @if(!empty($donation->message))
            <div class="donation-message">
                <p>{{ $donation->message }}</p>
            </div>
        @endif
    </div>
</li>


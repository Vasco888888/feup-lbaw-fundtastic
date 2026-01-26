@auth
<div id="appeal-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; max-width: 600px; border-radius: 4px;">
        <h3 style="color: #d9534f;">Appeal account ban</h3>

        <form id="appeal-form" method="POST" action="{{ route('appeals.store') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">

            <label for="appeal-reason">Reason *</label>
            <textarea id="appeal-reason" name="reason" rows="6" required maxlength="2000" placeholder="Explain why you believe your account was banned by mistake"></textarea>

            <div style="margin-top: 1rem; display:flex; gap:0.5rem; align-items:center;">
                <button type="submit" class="button" id="appeal-submit">Send appeal</button>
                <button type="button" class="button button-outline" id="appeal-cancel">Cancel</button>
                <span id="appeal-feedback" style="margin-left:auto;color:#706f6c;font-size:0.9rem;"></span>
            </div>
        </form>
    </div>
</div>
@else
    {{-- Guests cannot appeal; please log in --}}
@endauth

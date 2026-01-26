@extends('layouts.app')

@section('title', 'Administrator')

@section('content')
<div class="admin-container">
        <div class="admin-header">
            <h2>Administrator Dashboard</h2>
        </div>

        <div class="admin-search-row">
        <form id="admin-search-form" action="{{ route('admin.search') }}" method="GET" class="admin-search-form">
            <input id="admin-search-input" name="q" type="search" placeholder="Search users and admins by name or email" value="{{ isset($q) ? e($q) : '' }}" class="search-input" />

            <div class="admin-search-actions">
                <button class="button" type="submit">Search</button>
                <button id="admin-search-clear" type="button" class="button button-outline">Clear</button>
                <button id="create-user-open" class="button" type="button">Create User/Admin</button>
            </div>
        </form>
    </div>

    <!-- Users section: displays a scrollable list (shows ~5 rows) -->
    <section id="admin-users-section" class="admin-section">
        <h3>Users</h3>
        <div id="admin-users-list">
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="admin-users-list-body">
                    @if(isset($allUsers) && $allUsers->count())
                        @foreach($allUsers as $user)
                            <tr>
                                <td>{{ $user->user_id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <a class="button" href="{{ route('users.show', $user->user_id) }}">View</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="4" class="admin-empty-state">No users available.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>

    <script>
        // Expose the initial users to JS (minimal fields only).
        window.ADMIN_INITIAL_USERS = @json(isset($allUsers) ? $allUsers->map(function($u){ return ['id' => $u->user_id, 'name' => $u->name, 'email' => $u->email]; }) : []);
    </script>

    <!-- keep an empty results container for JS compatibility; searches will update the main Users table only -->
    <div id="admin-search-results"></div>

    <section class="admin-reports admin-section">
        <h3>Reports</h3>

        @if(isset($reports) && $reports->count())
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reason</th>
                        <th>Reporter (user_id)</th>
                        <th>Comment</th>
                        <th>Campaign</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->report_id }}</td>
                            <td>{{ $report->reason }}</td>
                            <td>{{ $report->user_id }}</td>
                            <td>{{ $report->comment_id ?: '—' }}</td>
                            <td>{{ $report->campaign_id ?: '—' }}</td>
                            <td>
                                @if($report->status === 'open')
                                    <span class="status-badge status-open">Open</span>
                                @elseif($report->status === 'resolved')
                                    <span class="status-badge status-resolved">Resolved</span>
                                @else
                                    <span class="status-badge">{{ $report->status ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>{{ $report->date ? \Carbon\Carbon::parse($report->date)->format('M d, Y') : '—' }}</td>
                            <td><div class="action-buttons">
                                @if(! empty($report->comment_id))
                                    <a class="button" href="{{ route('admin.reports.view_comment', $report->report_id) }}">View Comment</a>
                                @endif

                                @if(! empty($report->campaign_id))
                                    <a class="button" href="{{ route('campaigns.show', $report->campaign_id) }}" target="_blank">View Campaign</a>
                                @endif

                                <form action="{{ route('admin.reports.toggle', $report->report_id) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($report->status === 'open')
                                        <button class="button" type="submit">Mark Resolved</button>
                                    @else
                                        <button class="button" type="submit">Reopen</button>
                                    @endif
                                </form>
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="admin-empty-state">
                <p>No reports found.</p>
            </div>
        @endif
    </section>

    <section class="admin-appeals admin-section">
        <h3>Appeals</h3>

        @if(isset($appeals) && $appeals->count())
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reason</th>
                        <th>User (user_id)</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appeals as $appeal)
                        <tr>
                            <td>{{ $appeal->appeal_id }}</td>
                            <td>{{ $appeal->reason }}</td>
                            <td>{{ $appeal->user_id }}</td>
                            <td>
                                @if($appeal->status === 'pending' || empty($appeal->status))
                                    <span class="status-badge status-pending">Pending</span>
                                @elseif($appeal->status === 'accepted')
                                    <span class="status-badge status-accepted">Accepted</span>
                                @elseif($appeal->status === 'rejected')
                                    <span class="status-badge status-rejected">Rejected</span>
                                @else
                                    <span class="status-badge">{{ $appeal->status }}</span>
                                @endif
                            </td>
                            <td>{{ $appeal->date ? \Carbon\Carbon::parse($appeal->date)->format('M d, Y') : '—' }}</td>
                            <td><div class="action-buttons">
                                @if(! empty($appeal->user_id))
                                    <a class="button" href="{{ route('users.show', $appeal->user_id) }}">View User</a>
                                @endif

                                @if(empty($appeal->status) || $appeal->status === 'pending')
                                    <form action="{{ route('admin.appeals.accept', $appeal->appeal_id) }}" method="POST">
                                        @csrf
                                        <button class="button" type="submit">Accept</button>
                                    </form>

                                    <form action="{{ route('admin.appeals.reject', $appeal->appeal_id) }}" method="POST">
                                        @csrf
                                        <button class="button button-outline" type="submit">Reject</button>
                                    </form>
                                @endif
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="admin-empty-state">
                <p>No appeals found.</p>
            </div>
        @endif
    </section>

    </div> <!-- End admin-container -->
@endsection

<!-- Create User Modal -->
<div id="create-user-modal" class="admin-modal">
    <div class="admin-modal-content">
        <h3>Create User/Admin</h3>

        <form id="create-user-form">
            @csrf

            <div class="account-type-group">
                <label>Account Type *</label>
                <div class="account-type-options">
                    <label class="account-type-option">
                        <input type="radio" name="account_type" value="user" checked>
                        <span>User</span>
                    </label>
                    <label class="account-type-option">
                        <input type="radio" name="account_type" value="admin">
                        <span>Admin</span>
                    </label>
                </div>
            </div>

            <label for="cu-name">Name *</label>
            <input id="cu-name" name="name" type="text" maxlength="255" required>

            <label for="cu-email">Email *</label>
            <input id="cu-email" name="email" type="email" maxlength="255" required>

            <label for="cu-password">Password *</label>
            <input id="cu-password" name="password" type="password" minlength="8" required>

            <div style="margin-top: 1rem; display: flex; gap: 0.75rem; align-items: center;">
                <button type="submit" class="button" style="display: flex; align-items: center; justify-content: center;">Create</button>
                <button type="button" id="create-user-cancel" class="button button-outline" style="display: flex; align-items: center; justify-content: center;">Cancel</button>
            </div>

            <div id="create-user-message" style="margin-top:0.6rem;color:#c33"></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var open = document.getElementById('create-user-open');
        var modal = document.getElementById('create-user-modal');
        var cancel = document.getElementById('create-user-cancel');
        if (open && modal) {
            open.addEventListener('click', function(){ modal.style.display = 'block'; });
        }
        if (cancel && modal) {
            cancel.addEventListener('click', function(){ modal.style.display = 'none'; });
        }
        if (modal) {
            modal.addEventListener('click', function(e){ if (e.target === modal) modal.style.display = 'none'; });
        }

        // AJAX form submit
        var form = document.getElementById('create-user-form');
        if (form) {
            form.addEventListener('submit', function(e){
                e.preventDefault();
                var url = "{{ route('admin.users.store') }}";
                var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                var data = new FormData(form);

                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                    body: data
                }).then(function(resp){
                    return resp.json().then(function(json){ return {status: resp.status, body: json}; });
                }).then(function(result){
                    var msg = document.getElementById('create-user-message');
                    if (result.status >= 200 && result.status < 300 && result.body.success) {
                        msg.style.color = 'green';
                        msg.textContent = 'User created: ' + (result.body.user.name || result.body.user.email);
                        // reset form
                        form.reset();
                        // Close modal and reload so the new user appears in the users list
                        modal.style.display = 'none';
                        setTimeout(function(){ window.location.reload(); }, 600);
                    } else {
                        msg.style.color = '#c33';
                        if (result.body && result.body.errors) {
                            var first = Object.values(result.body.errors)[0];
                            msg.textContent = Array.isArray(first) ? first[0] : first;
                        } else if (result.body && result.body.message) {
                            msg.textContent = result.body.message;
                        } else {
                            msg.textContent = 'An error occurred.';
                        }
                    }
                }).catch(function(){
                    var msg = document.getElementById('create-user-message');
                    msg.style.color = '#c33';
                    msg.textContent = 'Network error.';
                });
            });
        }
    });
</script>
<script>
    // AJAX search for Admin dashboard
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('admin-search-form');
        var input = document.getElementById('admin-search-input');
        var resultsContainer = document.getElementById('admin-search-results');
        var clearBtn = document.getElementById('admin-search-clear');

        if (! form || ! input || ! resultsContainer) return;

        function renderResults(data) {
            var users = data.users || [];
            // Only update the main users table. Do not show the separate "Search Results" panel.
            if (typeof renderUsers === 'function') renderUsers(users, true);
            // Ensure the (now-unused) results container is empty.
            resultsContainer.innerHTML = '';
        }

        function renderUsers(users) {
            var isSearch = false;
            if (arguments.length > 1) isSearch = !!arguments[1];
            var body = document.getElementById('admin-users-list-body');
            if (!body) return;

            var list = users;
            // If no users passed, decide whether to show 'no matches' (search)
            // or restore the initial list (non-search/clear).
            if (!list || !list.length) {
                if (isSearch) {
                    body.innerHTML = '<tr><td colspan="4">No users matched your search.</td></tr>';
                    return;
                }
                list = window.ADMIN_INITIAL_USERS || [];
            }

            if (!list.length) {
                body.innerHTML = '<tr><td colspan="4">No users available.</td></tr>';
                return;
            }

            var html = '';
            list.forEach(function(u){
                html += '<tr>';
                html += '<td>' + escapeHtml(u.id) + '</td>';
                html += '<td>' + escapeHtml(u.name) + '</td>';
                html += '<td>' + escapeHtml(u.email) + '</td>';
                html += '<td><a class="button" href="/users/' + encodeURIComponent(u.id) + '">View</a></td>';
                html += '</tr>';
            });

            body.innerHTML = html;
        }

        function csrfInput() {
            var tokenMeta = document.querySelector('meta[name="csrf-token"]');
            var token = tokenMeta ? tokenMeta.getAttribute('content') : '';
            return '<input type="hidden" name="_token" value="' + escapeHtml(token) + '">';
        }

        function escapeHtml(s) {
            return (s === null || s === undefined) ? '' : String(s).replace(/[&<>"'`]/g, function(c){
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'}[c];
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var q = input.value.trim();
            var url = new URL(form.action, window.location.origin);
            if (!q) {
                // empty query -> restore initial users and clear search results
                if (typeof renderUsers === 'function') renderUsers(null, false);
                resultsContainer.innerHTML = '';
                return;
            }
            if (q) url.searchParams.set('q', q);

            fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).then(function(resp){
                    if (!resp.ok) {
                        // server error: clear separate results panel and show 'no matches' in main table
                        if (typeof renderUsers === 'function') renderUsers([], true);
                        resultsContainer.innerHTML = '';
                        throw new Error('Network response not ok: ' + resp.status);
                    }
                // Try parse JSON; if server returned HTML (e.g. login redirect), show it.
                return resp.text().then(function(text){
                    try {
                        var json = text ? JSON.parse(text) : {};
                        return json;
                    } catch (e) {
                        // Unexpected response: clear separate results panel and show no matches
                        if (typeof renderUsers === 'function') renderUsers([], true);
                        resultsContainer.innerHTML = '';
                        throw e;
                    }
                });
            }).then(function(data){
                renderResults(data);
            }).catch(function(err){
                if (!resultsContainer.innerHTML) {
                    resultsContainer.innerHTML = '<section style="margin-top:1rem;"><p>Error fetching results.</p></section>';
                }
                console.warn('Search error', err);
            });
        });

        // Clear button restores the initial users list and clears results.
        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                input.value = '';
                // restore initial users without hitting the server
                if (typeof renderUsers === 'function') renderUsers(null, false);
                // clear search results area
                if (resultsContainer) resultsContainer.innerHTML = '';
                input.focus();
            });
        }
    });
</script>
@endpush

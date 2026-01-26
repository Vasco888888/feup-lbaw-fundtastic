@extends('layouts.app')

@section('title', 'Search Users')

@section('content')
@php
    $initialUsers = isset($users) ? $users->map(function($u) {
        return [
            'id' => $u->user_id,
            'name' => $u->name,
            'email' => $u->email,
            'profile_image' => $u->profile_image
        ];
    })->toArray() : [];
@endphp
<div class="users-search-page">
    <div class="users-search-hero">
        <div class="users-search-hero-content">
            <h2>Find Users</h2>
            <p class="users-search-subtitle">Search for users by name or email address</p>
            
            <div class="users-search-form-wrapper">
                <form id="user-search-form" action="{{ route('users.search') }}" method="GET" class="users-search-form">
                    <input id="user-search-input" name="q" type="search" placeholder="Search users by name or email" value="{{ isset($q) ? e($q) : '' }}" class="users-search-input" />

                    <div class="users-search-actions">
                        <button class="button" type="submit">Search</button>
                        <button id="user-search-clear" type="button" class="button button-outline">Clear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="users-content">
        <div class="users-section-card">
            <div class="users-section-header">
                <h3>Search Results</h3>
            </div>
            <div class="users-table-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="users-list-body">
                        @if(isset($users) && $users->count())
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <img src="{{ $user->profile_image }}" alt="{{ $user->name }}" class="user-avatar" />
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <a class="button" href="{{ route('users.show', $user->user_id) }}">View Profile</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4" class="no-results">{{ isset($q) && $q !== '' ? 'No users found matching your search.' : 'Enter a search term to find users.' }}</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <script>
        window.INITIAL_USERS = @json($initialUsers);
    </script>

    <div id="user-search-results"></div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var form = document.getElementById('user-search-form');
        var input = document.getElementById('user-search-input');
        var resultsContainer = document.getElementById('user-search-results');
        var clearBtn = document.getElementById('user-search-clear');

        if (! form || ! input || ! resultsContainer) return;

        function renderResults(data) {
            var users = data.users || [];
            if (typeof renderUsers === 'function') renderUsers(users, true);
            resultsContainer.innerHTML = '';
        }

        function renderUsers(users) {
            var isSearch = false;
            if (arguments.length > 1) isSearch = !!arguments[1];
            var body = document.getElementById('users-list-body');
            if (!body) return;

            var list = users;

            if (!list || !list.length) {
                if (isSearch) {
                    body.innerHTML = '<tr><td colspan="4" class="no-results">No users matched your search.</td></tr>';
                    return;
                }
                list = window.INITIAL_USERS || [];
            }

            if (!list.length) {
                body.innerHTML = '<tr><td colspan="4" class="no-results">Enter a search term to find users.</td></tr>';
                return;
            }

            var html = '';
            list.forEach(function(u){
                html += '<tr>';
                html += '<td><img src="' + escapeHtml(u.profile_image || '/images/defaultpfp.svg') + '" alt="' + escapeHtml(u.name) + '" class="user-avatar" /></td>';
                html += '<td>' + escapeHtml(u.name) + '</td>';
                html += '<td>' + escapeHtml(u.email) + '</td>';
                html += '<td><a class="button" href="/users/' + encodeURIComponent(u.id) + '">View Profile</a></td>';
                html += '</tr>';
            });

            body.innerHTML = html;
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
                    if (typeof renderUsers === 'function') renderUsers([], true);
                    resultsContainer.innerHTML = '';
                    throw new Error('Network response not ok: ' + resp.status);
                }
                return resp.text().then(function(text){
                    try {
                        var json = text ? JSON.parse(text) : {};
                        return json;
                    } catch (e) {
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

        if (clearBtn) {
            clearBtn.addEventListener('click', function (e) {
                input.value = '';
                if (typeof renderUsers === 'function') renderUsers(null, false);
                if (resultsContainer) resultsContainer.innerHTML = '';
                input.focus();
            });
        }
    });
</script>
@endpush

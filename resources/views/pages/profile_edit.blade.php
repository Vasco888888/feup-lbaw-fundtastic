@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="profile-edit-container">
    <div class="profile-edit-header">
        <h2>Edit Profile</h2>
    </div>

    @if(session('success'))
        <div class="profile-edit-alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="profile-edit-alert-error">
            <strong>Please fix the following errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Profile Picture Upload Section --}}
    <div class="profile-edit-section">
        <h3 class="profile-edit-section-header">Profile Picture</h3>
        
        @php
            $currentProfilePicture = null;
            if ($user->profile_media_id && $user->profileMedia) {
                $currentProfilePicture = asset($user->profileMedia->file_path);
            }
        @endphp

        @if($currentProfilePicture)
            <div class="profile-picture-current">
                <img src="{{ $currentProfilePicture }}" alt="Current profile picture" />
            </div>

            @if($user->profile_media_id)
                <form method="POST" action="{{ route('profile.picture.remove') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button profile-picture-remove-btn" 
                            onclick="return confirm('Are you sure you want to remove your profile picture?')">
                        Remove Profile Picture
                    </button>
                </form>
            @endif
        @else
            <div class="profile-picture-no-image">
                <p>No profile picture uploaded</p>
            </div>
        @endif

        <form id="profile-picture-form" method="POST" action="{{ route('profile.picture.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="profile-picture-upload-area" onclick="document.getElementById('profile-picture-input').click()">
                <svg class="profile-picture-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="profile-picture-upload-title">Click to upload a new profile picture</p>
                <p class="profile-picture-upload-subtitle">JPG, JPEG, PNG or GIF (max 5MB)</p>
            </div>
            <input type="file" id="profile-picture-input" name="profile_picture" accept="image/jpeg,image/jpg,image/png,image/gif" 
                   style="display:none" onchange="document.getElementById('profile-picture-form').submit()">
        </form>
    </div>

    <div class="profile-edit-section">
        <h3 class="profile-edit-section-header">Profile Information</h3>
        
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            <div class="profile-edit-form-group">
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="profile-edit-form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="profile-edit-form-group">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <div class="profile-edit-actions">
                <button class="button" type="submit">Save Changes</button>
                @if(isset($user->user_id))
                    <a class="button button-outline" href="{{ route('users.show', $user->user_id) }}">Cancel</a>
                @elseif(isset($user->admin_id) || (is_object($user) && $user instanceof \App\Models\Admin))
                    <a class="button button-outline" href="{{ route('admin.profile.show', isset($user->admin_id) ? $user->admin_id : (property_exists($user, 'admin_id') ? $user->admin_id : null)) }}">Cancel</a>
                @else
                    <a class="button button-outline" href="{{ route('profile') }}">Cancel</a>
                @endif
            </div>
        </form>
    </div>

    @if(isset($user->user_id))
    {{-- Delete Account Section (User Only) --}}
    <div class="profile-edit-delete-section">
        <h3>Delete Account</h3>
        <p>Once you delete your account, there is no going back. This will anonymize all your content and permanently remove your account.</p>
        <form method="POST" action="{{ route('profile.destroy') }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete your account? This will anonymize all your content and remove the account permanently. This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="button profile-edit-delete-btn">Delete My Account</button>
        </form>
    </div>
    @endif
</div>
@endsection

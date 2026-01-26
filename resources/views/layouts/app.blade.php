<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Open Graph / Twitter share meta (page can override via sections) -->
        <meta property="og:title" content="@yield('meta_title', config('app.name', 'Laravel'))">
        <meta property="og:description" content="@yield('meta_description', 'Support great causes on FundTastic!')">
        <meta property="og:type" content="@yield('meta_type', 'website')">
        <meta property="og:url" content="@yield('meta_url', url()->current())">
        <meta property="og:image" content="@yield('meta_image', asset('images/default_share.png'))">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="@yield('meta_title', config('app.name', 'Laravel'))">
        <meta name="twitter:description" content="@yield('meta_description', 'Support great causes on FundTastic!')">
        <meta name="twitter:image" content="@yield('meta_image', asset('images/default_share.png'))">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Favicon / Touch Icon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/milligram.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @stack('styles')

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
        @stack('scripts')

        <style>
            body {
                min-height: 100vh;
                margin: 0;
                display: flex;
                flex-direction: column;
            }
            
            body > main {
                flex: 1;
                display: flex;
                flex-direction: column;
            }
        </style>
    </head>
    <body>
        <!-- Loading Spinner -->
        <div id="loading-spinner" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); z-index:9999;">
            <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <main>
            <header class="modern-header">
                <div class="header-container">
                    <div class="header-logo">
                        <a href="{{ route('landing') }}" class="logo-link">
                            <img src="{{ asset('images/logo.png') }}" alt="FundTastic Logo" class="logo-img" />
                            <span class="logo-text">FundTastic</span>
                        </a>
                    </div>

                    @php
                        $isUser = Auth::check();
                        // Consider either the admin guard or a legacy/session flag set at login.
                        $isAdmin = Auth::guard('admin')->check() || session('is_admin');
                    @endphp

                    <nav class="header-nav">
                    @if($isUser || $isAdmin)
                        {{-- Administrator page link for admins (left side) --}}
                        @if($isAdmin)
                            <a class="nav-button admin-button" href="{{ route('admin.index') }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                                <span>Administrator</span>
                            </a>
                        @endif

                        {{-- Search users link for regular users --}}
                        @if(! $isAdmin)
                            <a class="nav-button" href="{{ route('users.search') }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                                <span>Search Users</span>
                            </a>
                        @endif

                        {{-- Show create only to regular authenticated users (not admins) --}}
                        @if($isUser && ! $isAdmin)
                            <a class="nav-button create-button" href="{{ route('campaigns.create') }}">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                <span>Create Campaign</span>
                            </a>
                        @endif

                        {{-- Notification bell for regular users only --}}
                        @if($isUser && ! $isAdmin)
                            @include('partials.notification-bell')
                        @endif

                        @php
                            // Prefer admin identity when available even if a web user guard is also active.
                            if ($isAdmin) {
                                $adminId = session('admin_id') ?? (Auth::guard('admin')->check() ? Auth::guard('admin')->id() : null);
                                $adminName = session('admin_name') ?? (Auth::guard('admin')->check() ? Auth::guard('admin')->user()->name : null);

                                if (empty($adminName) && $adminId) {
                                    try {
                                        $adminModel = \App\Models\Admin::find($adminId);
                                        $adminName = $adminModel?->name;
                                    } catch (\Throwable $e) {
                                        $adminName = null;
                                    }
                                }

                                $currentName = $adminName ?? 'Admin';
                                $currentHref = $adminId ? route('admin.profile.show', $adminId) : route('profile');
                            } elseif ($isUser) {
                                $currentName = Auth::user()->name;
                                $currentHref = route('users.show', Auth::id());
                            } else {
                                $currentName = 'Profile';
                                $currentHref = route('profile');
                            }
                        @endphp

                        @php
                            $profileImage = null;
                            if ($isAdmin) {
                                $profileImage = session('admin_profile_image') ?? null;
                            } elseif ($isUser) {
                                $profileImage = Auth::user()->profile_image ?? null;
                            }
                            $profileImage = $profileImage ?? asset('images/defaultpfp.svg');
                        @endphp

                        <a href="{{ $currentHref }}" class="header-profile">
                            <img src="{{ $profileImage }}" alt="{{ $currentName }}" class="profile-avatar" />
                            <span class="profile-name">{{ $currentName }}</span>
                        </a>
                    @else
                        <a class="nav-button login-button" href="{{ route('login') }}">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <polyline points="10 17 15 12 10 7"></polyline>
                                <line x1="15" y1="12" x2="3" y2="12"></line>
                            </svg>
                            <span>Login</span>
                        </a>
                    @endif
                    </nav>
                </div>
            </header>
            
            <style>
                .modern-header {
                    background: white;
                    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
                    position: sticky;
                    top: 0;
                    z-index: 100;
                    border-bottom: 1px solid #e2e8f0;
                }
                
                .header-container {
                    max-width: 100%;
                    margin: 0 auto;
                    padding: 0.75rem 2rem;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 2rem;
                }
                
                .header-logo .logo-link {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    text-decoration: none;
                    transition: opacity 0.2s ease;
                }
                
                .header-logo .logo-link:hover {
                    opacity: 0.85;
                }
                
                .header-logo .logo-img {
                    height: 2rem;
                    width: auto;
                }
                
                .header-logo .logo-text {
                    font-size: 1.5rem;
                    font-weight: 800;
                    background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    letter-spacing: -0.02em;
                }
                
                .header-nav {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                }
                
                .nav-button {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.65rem 1.25rem;
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: 0.95rem;
                    text-decoration: none;
                    transition: all 0.2s ease;
                    border: 2px solid transparent;
                    color: #475569;
                    background: transparent;
                }
                
                .nav-button:hover {
                    background: #f8fafc;
                    color: #1e293b;
                }
                
                .nav-button svg {
                    flex-shrink: 0;
                }
                
                .nav-button.create-button {
                    background: linear-gradient(135deg, #1aa37a 0%, #158f69 100%);
                    color: white;
                    border: none;
                    box-shadow: 0 2px 8px rgba(26, 163, 122, 0.25);
                }
                
                .nav-button.create-button:hover {
                    background: linear-gradient(135deg, #158f69 0%, #127a5a 100%);
                    box-shadow: 0 4px 12px rgba(26, 163, 122, 0.35);
                    transform: translateY(-1px);
                }
                
                .nav-button.admin-button {
                    background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                    color: white;
                    border: none;
                    box-shadow: 0 2px 8px rgba(99, 102, 241, 0.25);
                }
                
                .nav-button.admin-button:hover {
                    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
                    transform: translateY(-1px);
                }
                
                .nav-button.login-button {
                    background: white;
                    color: #0d7db8;
                    border: 2px solid #0d7db8;
                }
                
                .nav-button.login-button:hover {
                    background: #0d7db8;
                    color: white;
                }
                
                .header-profile {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 0.5rem 1rem;
                    border-radius: 10px;
                    text-decoration: none;
                    transition: all 0.2s ease;
                    background: #f8fafc;
                    border: 2px solid transparent;
                }
                
                .header-profile:hover {
                    background: #f1f5f9;
                    border-color: #cbd5e1;
                    transform: translateY(-1px);
                }
                
                .header-profile .profile-avatar {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid white;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                
                .header-profile .profile-name {
                    font-weight: 600;
                    color: #1e293b;
                    font-size: 0.95rem;
                }
                
                @media (max-width: 1024px) {
                    .header-container {
                        padding: 1rem 1.5rem;
                    }
                    
                    .nav-button span {
                        display: none;
                    }
                    
                    .nav-button {
                        padding: 0.65rem;
                    }
                    
                    .header-profile .profile-name {
                        display: none;
                    }
                }
                
                @media (max-width: 768px) {
                    .header-logo .logo-text {
                        font-size: 1.5rem;
                    }
                    
                    .header-logo .logo-img {
                        height: 2rem;
                    }
                    
                    .header-container {
                        gap: 1rem;
                    }
                    
                    .header-nav {
                        gap: 0.5rem;
                    }
                }
            </style>

            @php
                // If a regular authenticated web user is banned, show a simple banned message
                $isBanned = false;
                if (Auth::check()) {
                    try {
                        $user = Auth::user();
                        if (isset($user->banned) && $user->banned) {
                            $isBanned = true;
                        }
                    } catch (\Throwable $e) {
                        $isBanned = false;
                    }
                }
            @endphp

            @if($isBanned)
                <section id="content">
                    <div style="padding:3rem;text-align:center">
                        <h2 style="color:#c00;margin-top:0">YOU ARE BANNED</h2>
                        <p class="muted">If you believe this is a mistake, contact an administrator.</p>
                        @auth
                            <div style="margin-top:1rem;">
                                <button id="appeal-open" class="button">Appeal ban</button>
                            </div>
                            <div style="margin-top:0.5rem;">
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="button button-secondary-modern">Logout</button>
                                </form>
                            </div>
                            @include('partials.appeal_modal')
                            @if(session('flash_message'))
                                <div style="margin-top:0.8rem;color:green">{{ session('flash_message') }}</div>
                            @endif
                            @if(session('flash_error'))
                                <div style="margin-top:0.8rem;color:#c33">{{ session('flash_error') }}</div>
                            @endif
                        @endauth
                    </div>
                </section>
            @else
                @include('partials.flash')

                <section id="content">
                    @yield('content')
                </section>
            @endif
            
            @include('partials.footer')
        </main>
    </body>
    <script>
        (function(){
            var open = document.getElementById('appeal-open');
            var modal = document.getElementById('appeal-modal');
            var cancel = document.getElementById('appeal-cancel');
            if (open && modal) {
                open.addEventListener('click', function(){ modal.style.display = 'block'; });
            }
            if (cancel && modal) {
                cancel.addEventListener('click', function(){ modal.style.display = 'none'; });
            }
            // Close modal when clicking outside content
            if (modal) {
                modal.addEventListener('click', function(e){
                    if (e.target === modal) modal.style.display = 'none';
                });
            }
        })();
    </script>
    <script src="{{ asset('js/animations.js') }}" defer></script>
    <!-- Spinner AJAX logic -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).ajaxStart(function() {
                $('#loading-spinner').show();
            }).ajaxStop(function() {
                $('#loading-spinner').hide();
            });
        });
    </script>
</html>
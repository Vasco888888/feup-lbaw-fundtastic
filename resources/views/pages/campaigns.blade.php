@extends('layouts.app')

@section('content')
    @include('partials.flash')
<section id="campaigns" class="campaigns-page">
    <style>
        .campaigns-page {
            margin: 0 auto;
            max-width: 100%;
            padding: 0;
        }
        
        .campaigns-hero {
            background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
            padding: 4rem 2rem;
            margin: 0 0 3rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .campaigns-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 60%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            pointer-events: none;
            animation: float 20s ease-in-out infinite;
        }
        
        .campaigns-hero::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-20px, -20px) scale(1.1); }
        }
        
        .campaigns-hero-content {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .campaigns-hero h2 {
            color: white;
            font-size: 3rem;
            font-weight: 800;
            margin: 0 0 1rem 0;
            line-height: 1.1;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 20px rgba(0,0,0,0.2);
            animation: slideInDown 0.8s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .campaigns-tagline {
            color: white !important;
            font-size: 1.2rem;
            font-weight: 500;
            margin: 0 0 2.5rem 0;
            max-width: 650px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 10px rgba(0,0,0,0.25);
            line-height: 1.6;
            display: block;
            opacity: 1;
            animation: slideInDown 0.8s ease-out 0.2s both;
        }
        
        .campaigns-search {
            display: flex;
            gap: 0.75rem;
            align-items: stretch;
            flex-wrap: wrap;
            max-width: 100%;
            background: white;
            padding: 0.6rem;
            border-radius: 16px;
            box-shadow: 0 25px 70px rgba(0,0,0,0.2), 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
            z-index: 3;
            animation: slideInUp 0.8s ease-out 0.4s both;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .campaigns-search .category-filter {
            padding: 0.75rem 1.25rem;
            border: 2px solid transparent;
            border-radius: 8px;
            font-size: 1rem;
            margin: 0;
            background: #f8fafb;
            cursor: pointer;
            min-width: 180px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .campaigns-search .category-filter:focus,
        .campaigns-search .category-filter:hover {
            border-color: #1aa37a;
            background: white;
            outline: none;
        }
        
        .campaigns-search .search-input {
            flex: 1;
            min-width: 280px;
            padding: 0.75rem 1.25rem;
            border: 2px solid transparent;
            border-radius: 8px;
            font-size: 1rem;
            margin: 0;
            background: #f8fafb;
            transition: all 0.2s ease;
        }
        
        .campaigns-search .search-input:focus {
            border-color: #1aa37a;
            background: white;
            outline: none;
        }
        
        .campaigns-search .search-input::placeholder {
            color: #94a3b8;
        }
        
        .campaigns-search .search-button {
            background: linear-gradient(135deg, #1aa37a 0%, #158f69 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(26, 163, 122, 0.3);
        }
        
        .campaigns-search .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 163, 122, 0.4);
        }
        
        .campaigns-search .search-clear {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin: 0;
            transition: all 0.2s ease;
        }
        
        .campaigns-search .search-clear:hover {
            border-color: #cbd5e1;
            background: #f8fafb;
        }
        
        .campaigns-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 4rem;
        }
        
        .campaigns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 2rem;
        }
        
        .campaign-card {
            background: white;
            border-radius: 20px;
            overflow: visible;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            border: 1px solid rgba(0,0,0,0.05);
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .campaign-card:nth-child(1) { animation-delay: 0.1s; }
        .campaign-card:nth-child(2) { animation-delay: 0.2s; }
        .campaign-card:nth-child(3) { animation-delay: 0.3s; }
        .campaign-card:nth-child(4) { animation-delay: 0.4s; }
        .campaign-card:nth-child(5) { animation-delay: 0.5s; }
        .campaign-card:nth-child(6) { animation-delay: 0.6s; }
        
        .campaign-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0,0,0,0.2);
        }
        
        .campaign-card-cover {
            position: relative;
            width: 100%;
            height: 240px;
            background-size: cover;
            background-position: center;
            background-color: #e8f2f0;
            transition: transform 0.5s ease;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .campaign-card:hover .campaign-card-cover {
            transform: scale(1.05);
        }
        
        .campaign-card-cover::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 100%);
            pointer-events: none;
            transition: background 0.5s ease;
        }
        
        .campaign-card-content {
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
            flex: 1;
            background: white;
            position: relative;
        }
        
        .campaign-card header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
        }
        
        .campaign-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            color: #0d7db8;
            background: rgba(13,125,184,0.1);
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
        }
        
        .campaign-status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            text-transform: capitalize;
        }
        
        .campaign-status.status-active {
            background: rgba(26, 163, 122, 0.15);
            color: #148760;
        }
        
        .campaign-status.status-completed {
            background: rgba(100, 116, 139, 0.15);
            color: #475569;
        }
        
        .campaign-status.status-suspended {
            background: rgba(251, 146, 60, 0.15);
            color: #ea580c;
        }
        
        .campaign-status.status-cancelled {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }
        
        .campaign-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.3s ease;
        }
        
        .campaign-card:hover h3 {
            color: #0d7db8;
        }
        
        .campaign-card-summary {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 60px;
        }
        
        .campaign-card-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }
        
        .campaign-card-meta img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        
        .campaign-card-meta strong {
            font-weight: 600;
            color: #334155;
        }
        
        .campaign-card-meta a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .campaign-card-meta a:hover {
            color: #0d7db8;
        }
        
        .campaign-card-progress {
            margin-top: auto;
        }
        
        .campaign-card-progress .progress-track {
            width: 100%;
            height: 8px;
            border-radius: 999px;
            background: #e2e8f0;
            overflow: hidden;
            position: relative;
        }
        
        .campaign-card-progress .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #0d7db8 0%, #1aa37a 100%);
            border-radius: 999px;
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 2px 8px rgba(13, 125, 184, 0.3);
        }
        
        .campaign-card-progress .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2.5s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .campaign-card-progress .progress-numbers {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            margin-top: 0.5rem;
        }
        
        .campaign-card-stats {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            padding: 0;
            margin: 1rem 0 0 0;
            border-top: 1px solid #f1f5f9;
            padding-top: 1rem;
        }
        
        .campaign-card-stats li {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .campaign-card-stats .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-weight: 600;
        }
        
        .campaign-card-stats .stat-value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .campaign-card .card-link {
            position: absolute;
            inset: 0;
            z-index: 1;
            border-radius: inherit;
        }
        
        .campaign-card a:not(.card-link) {
            position: relative;
            z-index: 2;
        }
        
        .campaign-card .card-link:focus {
            outline: 3px solid rgba(26, 163, 122, 0.4);
            outline-offset: 2px;
        }
        
        .no-campaigns-message {
            grid-column: 1 / -1;
            text-align: center;
            padding: 5rem 2rem;
            background: linear-gradient(135deg, #f8fafb 0%, #ffffff 100%);
            border-radius: 20px;
            border: 2px dashed #cbd5e1;
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .no-campaigns-message .muted {
            font-size: 1.25rem;
            color: #64748b;
            font-weight: 500;
            margin: 0;
        }
        
        .no-campaigns-message strong {
            color: #1aa37a;
            font-weight: 700;
        }
        
        .campaigns-grid.loading {
            opacity: 0.5;
            pointer-events: none;
        }
        
        .skeleton-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        
        .skeleton-card-cover {
            width: 100%;
            height: 240px;
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmerSkeleton 1.5s infinite;
        }
        
        @keyframes shimmerSkeleton {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        
        .skeleton-card-content {
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .skeleton-line {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmerSkeleton 1.5s infinite;
            border-radius: 4px;
            height: 16px;
        }
        
        .skeleton-line.wide {
            width: 100%;
        }
        
        .skeleton-line.medium {
            width: 70%;
        }
        
        .skeleton-line.short {
            width: 40%;
        }
        
        .skeleton-line.tall {
            height: 24px;
        }
        
        @media (max-width: 768px) {
            .campaigns-hero h2 {
                font-size: 2.2rem;
            }
            
            .campaigns-tagline {
                font-size: 1.05rem;
            }
            
            .campaigns-hero {
                padding: 2.5rem 1.5rem 2rem;
            }
            
            .campaigns-grid {
                grid-template-columns: 1fr;
                gap: 1.75rem;
            }
            
            .campaigns-search {
                flex-direction: column;
                padding: 0.75rem;
            }
            
            .campaigns-search .category-filter,
            .campaigns-search .search-input {
                min-width: 100%;
                padding: 0.9rem 1.25rem;
                font-size: 1rem;
            }
            
            .campaigns-search .search-button,
            .campaigns-search .search-clear {
                min-width: 100%;
                padding: 0.9rem 2rem;
                font-size: 1rem;
            }
            
            .campaign-card-cover {
                height: 200px;
            }
        }
        
        @media (max-width: 480px) {
            .campaigns-hero h2 {
                font-size: 1.8rem;
            }
            
            .campaigns-tagline {
                font-size: 0.95rem;
            }
            
            .campaign-card h3 {
                font-size: 1.3rem;
            }
        }
    </style>
    
    <div class="campaigns-hero">
        <div class="campaigns-hero-content">
            <h2>Discover Campaigns</h2>
            <p class="campaigns-tagline">Support the causes making a real difference in the world</p>
            
            <form id="campaigns-search-form" method="GET" action="{{route('campaigns.index')}}" class="campaigns-search">
                <select name="category" id="campaigns-category-filter" class="category-filter">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{$category->category_id}}" {{$selectedCategory == $category->category_id ? 'selected' : ''}}>
                            {{$category->name}}
                        </option>
                    @endforeach
                </select>
                
                <input 
                    type="text" 
                    name="search" 
                    id="campaigns-search-input"
                    placeholder="Search campaigns, categories, creators..." 
                    value="{{$search ?? ''}}"
                    class="search-input"
                />
                <button type="submit" class="search-button">Search</button>
                <a href="{{route('campaigns.index') }}" class="search-clear" id="campaigns-search-clear" style="{{empty($search) && empty($selectedCategory) ? 'display: none;' : ''}}">Clear</a>
            </form>
        </div>
    </div>
    
    <div class="campaigns-container">
        <div id="campaigns-grid" class="campaigns-grid">
            @forelse($campaigns as $campaign)
                @php
                    $progress = $campaign->goal_amount > 0
                        ? min(100, round(($campaign->current_amount / $campaign->goal_amount) * 100, 1))
                        : 0;
                @endphp

                @php $coverUrl = $campaign->coverMedia?->file_path; @endphp
                <article class="campaign-card">
                    <div class="campaign-card-cover" style="background-image: {{ $coverUrl ? 'url(' . e($coverUrl) . ')' : 'url(' . asset('images/logo.png') . ')' }};"></div>
                    
                    <div class="campaign-card-content">
                        <header>
                            <span class="campaign-category">{{$campaign->category?->name ?? 'Uncategorized'}}</span>
                            <span class="campaign-status status-{{$campaign->status}}">{{ucfirst($campaign->status) }}</span>
                        </header>

                        <h3>{{$campaign->title}}</h3>

                        <p class="campaign-card-summary">
                            {{\Illuminate\Support\Str::limit($campaign->description, 120) }}
                        </p>

                        <div class="campaign-card-meta">
                            <span style="color:#94a3b8;">by</span>
                            @if($campaign->creator)
                                <a href="{{ route('users.show', $campaign->creator->user_id) }}" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;color:inherit;">
                                    <img src="{{ $campaign->creator->profile_image ?? asset('images/defaultpfp.svg') }}" alt="{{ $campaign->creator->name }}" />
                                    <strong>{{ $campaign->creator->name }}</strong>
                                </a>
                            @else
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <img src="{{ asset('images/defaultpfp.svg') }}" alt="Anonymous" />
                                    <strong>Anonymous</strong>
                                </div>
                            @endif
                        </div>

                        <div class="campaign-card-progress">
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="progress-numbers">
                                <span>€{{number_format((float) $campaign->current_amount, 0, '.', ' ') }} raised</span>
                                <span>{{ number_format($progress, 0) }}%</span>
                            </div>
                        </div>

                        <ul class="campaign-card-stats">
                            <li>
                                <span class="stat-label">Goal</span>
                                <span class="stat-value">€{{number_format((float) $campaign->goal_amount, 0, '.', ' ')}}</span>
                            </li>
                            <li>
                                <span class="stat-label">Ends</span>
                                <span class="stat-value">{{ $campaign->end_date?->format('d M Y') ?? 'No deadline'}}</span>
                            </li>
                        </ul>
                    </div>
                    
                    <a class="card-link" href="{{ route('campaigns.show', $campaign->campaign_id) }}" aria-label="View campaign {{ $campaign->title }}"></a>
                </article>
            @empty
                <div class="no-campaigns-message">  
                    <p class="muted"> 
                        @if(!empty($search))
                            No campaigns found matching "<strong>{{$search}}</strong>".  
                        @else  
                            No campaigns found. 
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('campaigns-search-input');
    const categoryFilter = document.getElementById('campaigns-category-filter');
    const searchForm = document.getElementById('campaigns-search-form');
    const clearBtn = document.getElementById('campaigns-search-clear');
    const campaignsGrid = document.getElementById('campaigns-grid');

    function showLoadingState() {
        campaignsGrid.classList.add('loading');
        campaignsGrid.innerHTML = Array(6).fill(0).map(() => `
            <div class="skeleton-card">
                <div class="skeleton-card-cover"></div>
                <div class="skeleton-card-content">
                    <div class="skeleton-line short"></div>
                    <div class="skeleton-line wide tall"></div>
                    <div class="skeleton-line wide"></div>
                    <div class="skeleton-line medium"></div>
                    <div style="margin-top: 1rem;">
                        <div class="skeleton-line wide" style="height: 8px;"></div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function fetchCampaigns() {
        const searchValue = searchInput.value.trim();
        const categoryValue = categoryFilter.value;

        const params = new URLSearchParams();
        if (searchValue) params.append('search', searchValue);
        if (categoryValue) params.append('category', categoryValue);

        if (searchValue||categoryValue) {
            clearBtn.style.display = 'flex';
        } else {
            clearBtn.style.display = 'none';
        }

        showLoadingState();

        fetch('{{ route("api.campaigns.search")}}?' + params.toString())
            .then(response => response.json())
            .then(data => {
                setTimeout(() => {
                    updateCampaignsGrid(data.campaigns);
                    campaignsGrid.classList.remove('loading');
                }, 300);
                
                const newUrl = params.toString() ? 
                    '{{route("campaigns.index")}}?' + params.toString() : 
                    '{{route("campaigns.index")}}';
                window.history.pushState({}, '', newUrl);
            })
            .catch(error => {
                console.error('Error fetching campaigns:', error);
                campaignsGrid.classList.remove('loading');
            });
    }

    function updateCampaignsGrid(campaigns) {
        if (campaigns.length === 0) {
            const searchValue = searchInput.value.trim();
            campaignsGrid.innerHTML = `
                <div class="no-campaigns-message">
                    <p class="muted">
                        ${searchValue ? `No campaigns found matching "<strong>${searchValue}</strong>".` : 'No campaigns found.'}
                    </p>
                </div>
            `;
            return;
        }

        campaignsGrid.innerHTML = campaigns.map((campaign, index) => {
            const creatorImage = (campaign.creator && campaign.creator.profile_image) || '{{ asset("images/defaultpfp.svg") }}';
            const creatorName = (campaign.creator && campaign.creator.name) || 'Anonymous';
            const creatorLink = campaign.creator && campaign.creator.user_id ? `{{ url('/users') }}/${campaign.creator.user_id}` : '#';
            const coverStyle = campaign.cover_image
                ? `background-image: url('${campaign.cover_image}')`
                : 'background-image: linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            const categoryName = (campaign.category && campaign.category.name) ? campaign.category.name : 'Uncategorized';
            const safeDescription = campaign.description || '';
            const description = safeDescription.length > 120 ? `${safeDescription.substring(0, 120)}...` : safeDescription;
            const animationDelay = `animation-delay: ${index * 0.1}s;`;

            return `
                <article class="campaign-card" style="${animationDelay}">
                    <div class="campaign-card-cover" style="${coverStyle};"></div>
                    
                    <div class="campaign-card-content">
                        <header>
                            <span class="campaign-category">${categoryName}</span>
                            <span class="campaign-status status-${campaign.status}">${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}</span>
                        </header>

                        <h3>${campaign.title}</h3>

                        <p class="campaign-card-summary">${description}</p>

                        <div class="campaign-card-meta">
                            <span style="color:#94a3b8;">by</span>
                            <a href="${creatorLink}" style="display:flex;align-items:center;gap:0.5rem;text-decoration:none;color:inherit;">
                                <img src="${creatorImage}" alt="${creatorName}" />
                                <strong>${creatorName}</strong>
                            </a>
                        </div>

                        <div class="campaign-card-progress">
                            <div class="progress-track">
                                <div class="progress-fill" style="width: ${campaign.progress}%"></div>
                            </div>
                            <div class="progress-numbers">
                                <span>€${Math.round(campaign.current_amount).toLocaleString('en')} raised</span>
                                <span>${Math.round(campaign.progress)}%</span>
                            </div>
                        </div>

                        <ul class="campaign-card-stats">
                            <li>
                                <span class="stat-label">Goal</span>
                                <span class="stat-value">€${Math.round(campaign.goal_amount).toLocaleString('en')}</span>
                            </li>
                            <li>
                                <span class="stat-label">Ends</span>
                                <span class="stat-value">${campaign.end_date ? new Date(campaign.end_date).toLocaleDateString('en-GB', {day: 'numeric', month: 'short', year: 'numeric'}) : 'No deadline'}</span>
                            </li>
                        </ul>
                    </div>
                    
                    <a class="card-link" href="{{ url('/campaigns') }}/${campaign.campaign_id}" aria-label="View campaign ${campaign.title}"></a>
                </article>
            `;
        }).join('');
    }

    categoryFilter.addEventListener('change', function() {
        fetchCampaigns();
    });

    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchCampaigns();
    });

    clearBtn.addEventListener('click', function(e) {
        e.preventDefault();
        searchInput.value = '';
        categoryFilter.value = '';
        fetchCampaigns();
    });
});
</script>
@endsection

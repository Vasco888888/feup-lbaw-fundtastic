@extends('layouts.app')

@section('content')
    @include('partials.flash')
    <div style="background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%); padding: 80px 20px 60px; position: relative; overflow: hidden;">
        <div style="max-width: 1200px; margin: 0 auto; position: relative; z-index: 1;">
            <h1 style="font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 900; color: #ffffff; margin-bottom: 20px; text-align: center; letter-spacing: -0.5px;">
                Turn Ideas Into Reality
            </h1>
            <p style="font-size: clamp(1rem, 2.5vw, 1.3rem); color: rgba(255,255,255,0.95); text-align: center; margin-bottom: 40px; max-width: 700px; margin-left: auto; margin-right: auto;">
                Discover inspiring projects and help make them possible
            </p>
            <form action="{{ route('campaigns.index') }}" method="GET" style="max-width: 800px; margin: 0 auto;">
                <div style="display: flex; align-items: center; background: #fff; border-radius: 50px; padding: 8px 16px; gap: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); transition: all 0.3s ease;">
                    <span style="display: flex; align-items: center; justify-content: center; height: 44px; width: 44px; margin-left: 8px;">
                        <svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' fill='none' viewBox='0 0 24 24'>
                            <circle cx='11' cy='11' r='8' stroke='#94a3b8' stroke-width='2'/>
                            <path stroke='#94a3b8' stroke-width='2' stroke-linecap='round' d='M21 21l-4.35-4.35'/>
                        </svg>
                    </span>
                    <input type="text" name="search" placeholder="Search campaigns..." style="border: none; outline: none; background: transparent; font-size: 1.15rem; flex: 2; min-width: 0; padding: 14px 12px; color: #1e293b; margin: 0;">
                    <button type="submit" style="background: linear-gradient(135deg, #1aa37a 0%, #0d7db8 100%); color: #fff; border: none; border-radius: 50px; padding: 0 38px; height: 48px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(26,163,122,0.3); margin: 0;">
                        SEARCH
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div style="background: #f8fafc; padding: 40px 20px; border-bottom: 1px solid #e2e8f0;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: center; gap: 180px;">
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 900; background: linear-gradient(135deg, #0d7db8, #1aa37a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $totalActiveCampaigns }}</div>
                <div style="color: #64748b; font-weight: 600; margin-top: 8px;">Active Campaigns</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 900; background: linear-gradient(135deg, #0d7db8, #1aa37a); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">€{{ number_format($totalRaised, 0, '.', ' ') }}</div>
                <div style="color: #64748b; font-weight: 600; margin-top: 8px;">Raised</div>
            </div>
        </div>
    </div>

    <div style="padding: 80px 20px; background: #ffffff;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: clamp(1.8rem, 4vw, 2.5rem); color: #1e293b; font-weight: 900; margin-bottom: 12px; position: relative; display: inline-block;">
                    Featured Campaigns
                    <div style="position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 80px; height: 4px; background: linear-gradient(90deg, #0d7db8, #1aa37a); border-radius: 2px;"></div>
                </h2>
                <p style="color: #64748b; font-size: 1.1rem; margin-top: 20px;">Discover the projects making a difference</p>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 32px; width: 100%;">
                @foreach($popularCampaigns as $campaign)
                    @php
                        $progress = $campaign->goal_amount > 0
                            ? min(100, round(($campaign->current_amount / $campaign->goal_amount) * 100, 1))
                            : 0;
                        $coverUrl = $campaign->coverMedia?->file_path;
                    @endphp
                    <div style="background: #fff; border-radius: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden; display: flex; flex-direction: column; transition: all 0.3s ease; border: 1px solid #e2e8f0; cursor: pointer;" onmouseover="this.style.transform='translateY(-8px)'; this.style.boxShadow='0 12px 30px rgba(0,0,0,0.12)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.05)';">
                        <div style="height: 220px; background-size: cover; background-position: center; position: relative; background-image: {{ $coverUrl ? 'url(' . e($coverUrl) . ')' : 'linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%)' }};">
                            <div style="position: absolute; top: 16px; left: 16px; background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; color: #0d7db8; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                {{ $campaign->category?->name ?? 'General' }}
                            </div>
                            @if($campaign->status === 'active')
                                <div style="position: absolute; top: 16px; right: 16px; background: linear-gradient(135deg, #10b981, #059669); padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; color: #fff; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    ● ACTIVE
                                </div>
                            @endif
                        </div>
                        <div style="padding: 24px; flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.3rem; font-weight: 800; margin: 0 0 12px 0; color: #1e293b; line-height: 1.4;">
                                {{ $campaign->title }}
                            </h3>
                            <p style="color: #64748b; font-size: 0.95rem; margin: 0 0 20px 0; line-height: 1.6; flex: 1;">
                                {{ \Illuminate\Support\Str::limit($campaign->description, 100) }}
                            </p>
                            <div style="margin-top: auto;">
                                <div style="width: 100%; height: 10px; background: #f1f5f9; border-radius: 999px; overflow: hidden; margin-bottom: 12px;">
                                    <div style="height: 100%; background: linear-gradient(90deg, #0d7db8 0%, #1aa37a 100%); border-radius: 999px; width: {{ $progress }}%; transition: width 0.5s ease;"></div>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 16px;">
                                    <div>
                                        <div style="font-size: 1.4rem; font-weight: 900; color: #1e293b;">
                                            €{{ number_format((float) $campaign->current_amount, 0, '.', ' ') }}
                                        </div>
                                        <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                                            of €{{ number_format((float) $campaign->goal_amount, 0, '.', ' ') }}
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 1.4rem; font-weight: 900; color: #1aa37a;">
                                            {{ number_format($progress, 0) }}%
                                        </div>
                                        <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">
                                            reached
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('campaigns.show', $campaign->campaign_id) }}" style="display: block; text-align: center; background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%); color: #fff; padding: 14px 24px; border-radius: 12px; font-weight: 700; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(13,125,184,0.2);" onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 20px rgba(13,125,184,0.3)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 15px rgba(13,125,184,0.2)';">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 80px 20px; text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-size: clamp(1.8rem, 4vw, 2.5rem); color: #ffffff; font-weight: 900; margin-bottom: 20px;">
                Have a Project in Mind?
            </h2>
            <p style="color: rgba(255,255,255,0.8); font-size: 1.1rem; margin-bottom: 32px; line-height: 1.6;">
                Create your own campaign and start raising funds to make your idea a reality
            </p>
            <a href="{{ route('campaigns.create') }}" style="display: inline-block; background: linear-gradient(135deg, #1aa37a 0%, #0d7db8 100%); color: #fff; padding: 16px 48px; border-radius: 50px; font-weight: 700; text-decoration: none; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 8px 25px rgba(26,163,122,0.3);" onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 12px 35px rgba(26,163,122,0.4)';" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 8px 25px rgba(26,163,122,0.3)';">
                Create Campaign
            </a>
        </div>
    </div>
@endsection
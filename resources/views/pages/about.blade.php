@extends('layouts.app')

@section('title', 'About - FundTastic')

@section('content')
<div class="about-page">
    <div class="about-container">
        <div class="about-header">
            <h1>About FundTastic</h1>
            <p class="about-subtitle">Helping causes, one campaign at a time</p>
        </div>

        <div class="about-content">
            <section class="about-section">
                <h2>Who We Are</h2>
                <p>
                    FundTastic is a crowdfunding platform dedicated to helping individuals and organizations 
                    bring their ideas to life. Whether you're an entrepreneur launching a new product, an artist 
                    funding a creative project, or a community organizer supporting a local cause, FundTastic 
                    provides the tools and support you need to succeed.
                </p>
            </section>

            <section class="about-section">
                <h2>Our Mission</h2>
                <p>
                    We believe that great ideas deserve to be realized, regardless of financial barriers. 
                    Our mission is to democratize access to funding by connecting passionate creators with supportive communities. Through our platform, we facilitate connections and enable collaborative success.
                </p>
            </section>

            <section class="about-section">
                <h2>What We Offer</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🚀</div>
                        <h3>Easy Campaign Creation</h3>
                        <p>Launch your campaign in minutes with our intuitive tools.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🤝</div>
                        <h3>Community Support</h3>
                        <p>Connect with backers who believe in your vision and want to help you succeed.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💡</div>
                        <h3>Transparent Process</h3>
                        <p>Track your progress with real-time updates.</p>
                    </div>
                </div>
            </section>

            <section class="about-section">
                <h2>Join Our Community</h2>
                <p>
                    Whether you're here to launch a campaign or support others, you're part of a community of innovators, creators, and supporters.
                </p>
                <div class="about-cta">
                    @guest
                        <a href="{{route('register') }}" class="btn-primary">Get Started</a>
                        <a href="{{ route('campaigns.index') }}" class="btn-secondary">Browse Campaigns</a>
                    @else
                        <a href="{{route('campaigns.create') }}" class="btn-primary">Create Campaign</a>
                        <a href="{{route('campaigns.index') }}" class="btn-secondary">Explore</a>
                    @endguest
                </div>
            </section>
        </div>
    </div>
</div>

<style>
    .about-page {
        padding: 3rem 1rem;
        min-height: calc(100vh - 300px);
    }
    
    .about-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    
    .about-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .about-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }
    
    .about-subtitle {
        font-size: 1.25rem;
        color: #64748b;
        font-weight: 500;
    }
    
    .about-content {
        display: flex;
        flex-direction: column;
        gap: 3rem;
    }
    
    .about-section h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }
    
    .about-section p {
        font-size: 1.05rem;
        line-height: 1.7;
        color: #475569;
        margin-bottom: 1rem;
    }
    
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .feature-card {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }
    
    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }
    
    .feature-card h3 {
        font-size: 1.15rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.75rem;
    }
    
    .feature-card p {
        font-size: 0.95rem;
        color: #64748b;
        line-height: 1.5;
        margin: 0;
    }
    
    .about-cta {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .btn-primary,
    .btn-secondary {
        padding: 0.875rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.2s ease;
        display: inline-block;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
        color: white;
        border: none;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 125, 184, 0.4);
    }
    
    .btn-secondary {
        background: white;
        color: #0d7db8;
        border: 2px solid #0d7db8;
    }
    
    .btn-secondary:hover {
        background: #f8fafc;
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .about-header h1 {
            font-size: 2rem;
        }
        
        .about-subtitle {
            font-size: 1.1rem;
        }
        
        .about-section h2 {
            font-size: 1.5rem;
        }
        
        .features-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

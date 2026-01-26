<footer class="site-footer">
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-brand">
                <img src="{{asset('images/logo.png')}}" alt="FundTastic Logo" class="footer-logo" />
                <span class="footer-brand-text">FundTastic</span>
            </div>
            
            <nav class="footer-nav">
                <a href="{{route('about') }}" class="footer-link">About</a>
                <a href="{{route('help') }}" class="footer-link">Help</a>
                <a href="{{ route('contacts') }}" class="footer-link">Contacts</a>
            </nav>
            
            <div class="footer-copyright">
                <p>&copy; {{date('Y')}} FundTastic. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<style>
    .site-footer {
        background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
        color: white;
        margin-top: auto;
        padding: 2rem 0 1.5rem;
    }
    
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    .footer-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.5rem;
    }
    
    .footer-brand {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .footer-logo {
        height: 2rem;
        width: auto;
        
    }
    
    .footer-brand-text {
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
        letter-spacing: -0.02em;
    }
    
    .footer-nav {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .footer-link {
        color: rgba(255, 255, 255, 0.9);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
        font-size: 0.95rem;
    }
    
    .footer-link:hover {
        color: white;
        text-decoration: underline;
    }
    
    .footer-copyright {
        text-align: center;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.8);
        margin-top: 0.5rem;
    }
    
    .footer-copyright p {
        margin: 0;
    }
    
    @media (max-width: 640px) {
        .footer-nav {
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }
        
        .site-footer {
            padding: 1.5rem 0 1rem;
        }
    }
</style>

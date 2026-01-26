@extends('layouts.app')

@section('title', 'Contacts - FundTastic')

@section('content')
<div class="contacts-page">
    <div class="contacts-container">
        <div class="contacts-header">
            <h1>Contact Us</h1>
            <p class="contacts-subtitle">Get in touch with our support team</p>
        </div>

        <div class="contacts-content">
            <section class="info-section">
                <h2>Contact Support</h2>
                <p class="info-text">
                    If you have any questions, concerns, or feedback about FundTastic, please reach out to any of our administrators. We're here to help!
                </p>
            </section>

            <section class="admins-section">
                <h2>Our Support Team</h2>
                @if($admins->count() > 0)
                    <div class="admins-grid">
                        @foreach($admins as $admin)
                            <div class="admin-card">
                                <div class="admin-name">{{ $admin->name }}</div>
                                <div class="admin-email">
                                    <a href="mailto:{{ $admin->email }}">{{ $admin->email }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="no-admins">No support team members available at the moment.</p>
                @endif
            </section>
        </div>
    </div>
</div>

<style>
    .contacts-page {
        padding: 3rem 1rem;
        min-height: calc(100vh - 300px);
    }
    
    .contacts-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .contacts-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .contacts-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }
    
    .contacts-subtitle {
        font-size: 1.25rem;
        color: #64748b;
        font-weight: 500;
    }
    
    .contacts-content {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }
    
    .info-section,
    .admins-section {
        background: white;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    }
    
    .info-section h2,
    .admins-section h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
    }
    
    .info-text {
        font-size: 1rem;
        line-height: 1.6;
        color: #475569;
        margin: 0;
    }
    
    .admins-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }
    
    .admin-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1.5rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .admin-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        border-color: #0d7db8;
    }
    
    .admin-name {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.75rem;
    }
    
    .admin-email a {
        font-size: 0.95rem;
        color: #0d7db8;
        text-decoration: none;
        transition: color 0.2s ease;
    }
    
    .admin-email a:hover {
        color: #1aa37a;
        text-decoration: underline;
    }
    
    .no-admins {
        font-size: 1rem;
        color: #64748b;
        text-align: center;
        padding: 2rem;
    }
    
    @media (max-width: 768px) {
        .contacts-header h1 {
            font-size: 2rem;
        }
        
        .contacts-subtitle {
            font-size: 1.1rem;
        }
        
        .admins-grid {
            grid-template-columns: 1fr;
        }
        
        .info-section,
        .admins-section {
            padding: 1.5rem;
        }
    }
</style>
@endsection

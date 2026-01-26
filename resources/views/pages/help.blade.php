@extends('layouts.app')

@section('title', 'Help - FundTastic')

@section('content')
<div class="help-page">
    <div class="help-container">
        <div class="help-header">
            <h1>Help Center</h1>
            <p class="help-subtitle">Find answers to your questions</p>
        </div>

        <div class="help-content">
            <section class="help-section">
                <h2>Getting Started</h2>
                <div class="faq-item">
                    <h3>How do I create a campaign?</h3>
                    <p>
                        To create a campaign, you need to be registered and logged in. Click on "Create Campaign" in the navigation menu, fill in the required information about your project, set your funding goal, and publish your campaign.
                    </p>
                </div>
                <div class="faq-item">
                    <h3>Is there a fee to create a campaign?</h3>
                    <p>
                        Creating a campaign on FundTastic is free.
                    </p>
                </div>
            </section>

            <section class="help-section">
                <h2>Making Contributions</h2>
                <div class="faq-item">
                    <h3>How do I support a campaign?</h3>
                    <p>
                        Simply browse the campaigns, select one that interests you, and click the "Donate Now" button.
                    </p>
                </div>
            </section>

            <section class="help-section">
                <h2>Collaborative Campaigns</h2>
                <div class="faq-item">
                    <h3>What are collaborative campaigns?</h3>
                    <p>
                        Collaborative campaigns allow multiple users to work together on a single campaign. The campaign creator can invite up to 5 collaborators to help manage the campaign, post updates, and engage with supporters.
                    </p>
                </div>
                <div class="faq-item">
                    <h3>How do I request to collaborate on a campaign?</h3>
                    <p>
                        If a campaign has fewer than 5 collaborators, you'll see a "Request to Collaborate" button on the campaign page. Click it to send a request to the campaign creator. They can approve or decline your request from their campaign management page.
                    </p>
                </div>
                <div class="faq-item">
                    <h3>What can collaborators do?</h3>
                    <p>
                        Collaborators can post campaign updates, upload media, and help manage the campaign. However, only the original campaign creator can edit core campaign details, delete the campaign, or manage collaboration requests.
                    </p>
                </div>
            </section>

            <section class="help-section">
                <h2>Campaign Management</h2>
                <div class="faq-item">
                    <h3>Can I edit my campaign after publishing?</h3>
                    <p>
                        You can edit your campaign details, add updates, and upload new media before any donations are received. Once your campaign has received donations, editing core campaign details is disabled to maintain transparency for your supporters. However, you can still post updates and add media.
                    </p>
                </div>
                <div class="faq-item">
                    <h3>How do I update my supporters?</h3>
                    <p>
                        You can post campaign updates from your campaign management page. Supporters who follow your campaign will be notified of new updates.
                    </p>
                </div>
            </section>

            <section class="help-section">
                <h2>Need More Help?</h2>
                <p>
                    If you couldn't find the answer to your question, please don't hesitate to contact our support team. We're here to help!
                </p>
                <div class="help-cta">
                    <a href="{{route('contacts')}}" class="btn-primary">Contact Support</a>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
    .help-page {
        padding: 3rem 1rem;
        min-height: calc(100vh - 300px);
    }
    
    .help-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .help-header {
        text-align: center;
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .help-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1rem;
    }
    
    .help-subtitle {
        font-size: 1.25rem;
        color: #64748b;
        font-weight: 500;
    }
    
    .help-content {
        display: flex;
        flex-direction: column;
        gap: 3rem;
    }
    
    .help-section h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
    }
    
    .faq-item {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        margin-bottom: 1rem;
    }
    
    .faq-item h3 {
        font-size: 1.15rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.75rem;
    }
    
    .faq-item p {
        font-size: 1rem;
        line-height: 1.6;
        color: #475569;
        margin: 0;
    }
    
    .help-section > p {
        font-size: 1.05rem;
        line-height: 1.7;
        color: #475569;
        margin-bottom: 1rem;
    }
    
    .help-cta {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .btn-primary {
        padding: 0.875rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.2s ease;
        display: inline-block;
        background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
        color: white;
        border: none;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(13, 125, 184, 0.4);
    }
    
    @media (max-width: 768px) {
        .help-header h1 {
            font-size: 2rem;
        }
        
        .help-subtitle {
            font-size: 1.1rem;
        }
        
        .help-section h2 {
            font-size: 1.5rem;
        }
    }
</style>
@endsection

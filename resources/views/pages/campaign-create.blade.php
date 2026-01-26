@extends('layouts.app')

@section('title', 'Create Campaign')

@push('styles')
<style>
  .campaign-create-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1.5rem;
  }

  .progress-steps {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    position: relative;
    padding: 0 0 1rem 0;
  }

  .progress-steps::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: #e9ecef;
    z-index: 0;
  }

  .progress-bar {
    position: absolute;
    top: 20px;
    left: 0;
    height: 2px;
    background: linear-gradient(90deg, #3498db, #2ecc71);
    z-index: 1;
    transition: width 0.3s ease;
  }

  .progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    position: relative;
    z-index: 2;
    flex: 1;
  }

  .step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .progress-step.active .step-circle {
    border-color: #3498db;
    background: #3498db;
    color: white;
  }

  .progress-step.completed .step-circle {
    border-color: #2ecc71;
    background: #2ecc71;
    color: white;
  }

  .step-label {
    font-size: 0.85rem;
    color: #6c757d;
    font-weight: 500;
  }

  .progress-step.active .step-label {
    color: #3498db;
    font-weight: 600;
  }

  .char-counter {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 4px;
    text-align: right;
  }

  .char-counter.warning {
    color: #f39c12;
  }

  .char-counter.danger {
    color: #e74c3c;
    font-weight: 600;
  }

  .image-upload-area {
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    background: #f8f9fa;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
  }

  .image-upload-area:hover {
    border-color: #3498db;
    background: #eef6fc;
  }

  .image-upload-area.drag-over {
    border-color: #2ecc71;
    background: #e8f8f0;
  }

  .image-preview-container {
    display: none;
    margin-top: 1rem;
  }

  .image-preview-container.has-image {
    display: block;
  }

  .image-preview {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    max-width: 100%;
    max-height: 400px;
    margin: 0 auto;
  }

  .image-preview img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 8px;
  }

  .remove-image {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(231, 76, 60, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .remove-image:hover {
    background: #c0392b;
    transform: scale(1.1);
  }

  .goal-preview {
    margin-top: 8px;
    padding: 12px;
    background: #eef6fc;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #2c3e50;
  }

  .goal-preview strong {
    color: #3498db;
    font-size: 1.1rem;
  }

  .form-section {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.5s ease forwards;
  }

  @keyframes fadeInUp {
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  textarea {
    width: 100%;
    word-wrap: break-word;
    word-break: break-word;
    white-space: pre-wrap;
    overflow-wrap: break-word;
    max-width: 100%;
    box-sizing: border-box;
    font-size: 0.95rem;
    line-height: 1.5;
    padding: 12px;
    min-height: 110px;
  }

  .panel {
    overflow: hidden;
    word-wrap: break-word;
  }

  .campaign-create-wrapper {
    overflow-x: hidden;
    max-width: 100%;
  }

  .campaign-create-wrapper * {
    word-wrap: break-word;
    overflow-wrap: break-word;
  }

  .validation-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    display: none;
  }

  .input-wrapper {
    position: relative;
  }

  .input-wrapper.valid .validation-icon.check {
    display: block;
    color: #2ecc71;
  }

  .input-wrapper.invalid .validation-icon.x {
    display: block;
    font-size: 0.9rem;
  }

  .estimated-timeline {
    margin-top: 8px;
    padding: 10px;
    background: #fff3cd;
    border-left: 3px solid #ffc107;
    border-radius: 4px;
    font-size: 0.9rem;
  }

  .form-step {
    display: none;
  }

  .form-step.active {
    display: block;
  }

  .step-navigation {
    display: flex;
    gap: 12px;
    justify-content: space-between;
    margin-top: 1.5rem;
  }

  .step-nav-left {
    display: flex;
    gap: 12px;
  }

  .step-nav-right {
    display: flex;
    gap: 12px;
  }

  .section-indicator {
    display: inline-block;
    padding: 6px 14px;
    background: #e9ecef;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 1rem;
  }

  .section-indicator.active {
    background: #3498db;
    color: white;
  }

  /* Enhanced Create Campaign Button */
  .button-create-campaign {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 32px;
    font-size: 1rem;
    font-weight: 700;
    color: white;
    background: linear-gradient(135deg, #0d7db8 0%, #1aa37a 100%);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(13, 125, 184, 0.4), 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .button-create-campaign:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(13, 125, 184, 0.5), 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .button-create-campaign:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(13, 125, 184, 0.3);
  }

  .button-create-campaign:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    box-shadow: none;
  }

  .button-create-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
  }

  .button-create-campaign:hover:not(:disabled) .button-create-icon {
    transform: scale(1.1) rotate(5deg);
  }

  .button-create-text {
    position: relative;
    z-index: 2;
    letter-spacing: 0.3px;
  }

  .button-create-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
  }

  .button-create-campaign:hover:not(:disabled) .button-create-shine {
    left: 100%;
  }

  /* Pulse animation for enabled button */
  @keyframes pulse-glow {
    0%, 100% {
      box-shadow: 0 4px 15px rgba(13, 125, 184, 0.4), 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    50% {
      box-shadow: 0 4px 20px rgba(26, 163, 122, 0.6), 0 2px 8px rgba(0, 0, 0, 0.1);
    }
  }

  .button-create-campaign:not(:disabled) {
    animation: pulse-glow 2s ease-in-out infinite;
  }

  /* Launch Celebration Effects */
  @keyframes confetti-fall {
    0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
    100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
  }

  .confetti {
    position: fixed;
    width: 10px;
    height: 10px;
    z-index: 10000;
    pointer-events: none;
  }

  .launch-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(13, 125, 184, 0.95) 0%, rgba(26, 163, 122, 0.95) 100%);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeInOverlay 0.5s ease;
  }

  @keyframes fadeInOverlay {
    from { opacity: 0; }
    to { opacity: 1; }
  }

  .launch-content {
    text-align: center;
    color: white;
    animation: zoomIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }

  @keyframes zoomIn {
    from {
      transform: scale(0.5);
      opacity: 0;
    }
    to {
      transform: scale(1);
      opacity: 1;
    }
  }

  .launch-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 2rem;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: rotateIcon 0.8s ease-in-out;
  }

  @keyframes rotateIcon {
    0% { transform: rotate(0deg) scale(0); }
    50% { transform: rotate(180deg) scale(1.1); }
    100% { transform: rotate(360deg) scale(1); }
  }

  .launch-icon svg {
    width: 60px;
    height: 60px;
    stroke: #0d7db8;
    stroke-width: 3;
  }

  .launch-title {
    font-size: 3rem;
    font-weight: 800;
    margin: 0 0 1rem 0;
    text-shadow: 0 4px 20px rgba(0,0,0,0.2);
  }

  .launch-message {
    font-size: 1.3rem;
    opacity: 0.95;
    max-width: 600px;
    margin: 0 auto;
  }

  .launch-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    margin: 2rem auto 0;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    to { transform: rotate(360deg); }
  }

</style>
@endpush

@section('content')
<div class="profile-shell">
  <header class="campaign-header">
    <div>
      <h2 style="margin:0;">Create Campaign</h2>
      <p class="muted" style="margin-top:0.5rem;">Launch your crowdfunding campaign and bring your ideas to life</p>
    </div>
    <div class="hero-cta">
      <a href="{{ route('campaigns.index') }}" class="button button-outline">Cancel</a>
    </div>
  </header>

  <div class="campaign-create-wrapper">
    <!-- Progress Steps -->
    <div class="progress-steps">
      <div class="progress-bar" id="progressBar" style="width: 0%"></div>
      <div class="progress-step" data-step="1">
        <div class="step-circle">1</div>
        <span class="step-label">Details</span>
      </div>
      <div class="progress-step" data-step="2">
        <div class="step-circle">2</div>
        <span class="step-label">Funding & Category</span>
      </div>
      <div class="progress-step" data-step="3">
        <div class="step-circle">3</div>
        <span class="step-label">Timeline</span>
      </div>
      <div class="progress-step" data-step="4">
        <div class="step-circle">4</div>
        <span class="step-label">Review</span>
      </div>
    </div>

    @if($errors->any())
      <div class="panel" style="border-left:4px solid #f3c6c6;margin-bottom:12px">
        <ul style="margin:0;padding-left:1.25rem">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('campaigns.store') }}" id="campaignForm">
      @csrf

      <!-- Step 1: Campaign Details -->
      <div class="form-step active" id="step1">
        <div class="panel form-section" style="animation-delay: 0.1s">
          <span class="section-indicator active">Step 1 of 4</span>
          <h3 style="margin-top:0;margin-bottom:1rem;font-size:1.2rem;font-weight:600;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px">
              <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
            Campaign Details
          </h3>
        
        <div style="margin-bottom:1.5rem">
          <label for="title">Campaign Title <span style="color:#e74c3c;">*</span></label>
          <div class="input-wrapper">
            <input 
              id="title" 
              name="title" 
              type="text" 
              value="{{ old('title') }}" 
              required 
              maxlength="255" 
              minlength="10"
              placeholder="e.g., Help Build a Community Garden"
              style="padding-right:40px"
            >
            <span class="validation-icon check">✓</span>
            <span class="validation-icon x">✗</span>
          </div>
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div class="muted" style="margin-top:6px;font-size:0.85rem">
              A great title is clear, concise, and captures what your campaign is about. <strong>Minimum 10 characters.</strong>
            </div>
            <div class="char-counter" id="titleCounter">0 / 255</div>
          </div>
        </div>

        <div style="margin-bottom:1.5rem">
          <label for="description">Description <span style="color:#e74c3c;">*</span></label>
          <textarea 
            id="description" 
            name="description" 
            rows="18" 
            required 
            minlength="50"
            placeholder="Tell your story...&#10;&#10;• What are you raising funds for?&#10;• Why is this important?&#10;• How will the funds be used?&#10;• What impact will this have?"
          >{{ old('description') }}</textarea>
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div class="muted" style="margin-top:6px;font-size:0.85rem">
              Share your vision and goals. <strong>Minimum 50 characters.</strong> Aim for at least 200 characters for best results.
            </div>
            <div class="char-counter" id="descCounter">0 characters</div>
          </div>
        </div>

        <div class="step-navigation">
          <div class="step-nav-left">
            <button type="button" class="button button-outline" id="saveDraftBtn" onclick="saveDraft()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              Save Draft
            </button>
          </div>
          <div class="step-nav-right">
            <a class="button button-outline" href="{{ route('campaigns.index') }}">Cancel</a>
            <button type="button" class="button" onclick="nextStep(2)">
              Next
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-left:6px">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </button>
          </div>
        </div>
        </div>
      </div>

      <!-- Step 2: Funding & Category -->
      <div class="form-step" id="step2">
        <div class="panel form-section" style="animation-delay: 0.2s">
          <span class="section-indicator active">Step 2 of 4</span>
          <h3 style="margin-top:0;margin-bottom:1rem;font-size:1.2rem;font-weight:600;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M12 6v6l4 2"></path>
            </svg>
            Funding & Category
          </h3>

        <div style="display:flex;gap:12px;margin-bottom:1.5rem;flex-wrap:wrap">
          <div style="flex:1;min-width:250px">
            <label for="goal_amount">Funding Goal (€) <span style="color:#e74c3c;">*</span></label>
            <input 
              id="goal_amount" 
              name="goal_amount" 
              type="number" 
              step="0.01" 
              min="0.01" 
              value="{{ old('goal_amount') }}" 
              required 
              placeholder="0.00"
            >
            <div class="muted" style="margin-top:6px;font-size:0.85rem">Set a realistic funding target for your campaign</div>
            <div class="goal-preview" id="goalPreview" style="display:none">
              You're aiming to raise <strong id="goalAmount">€0</strong>
            </div>
          </div>

          <div style="flex:1;min-width:250px">
            <label for="category_id">Category <span style="color:#e74c3c;">*</span></label>
            <select id="category_id" name="category_id" required>
              <option value="" disabled selected>-- Select a category --</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->category_id }}" @selected(old('category_id') == $cat->category_id)>{{ $cat->name }}</option>
              @endforeach
            </select>
            <div class="muted" style="margin-top:6px;font-size:0.85rem">Help people discover your campaign</div>
          </div>
        </div>

        <div class="step-navigation">
          <div class="step-nav-left">
            <button type="button" class="button button-outline" onclick="previousStep(1)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px">
                <polyline points="15 18 9 12 15 6"></polyline>
              </svg>
              Back
            </button>
            <button type="button" class="button button-outline" id="saveDraftBtn2" onclick="saveDraft()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              Save Draft
            </button>
          </div>
          <div class="step-nav-right">
            <a class="button button-outline" href="{{ route('campaigns.index') }}">Cancel</a>
            <button type="button" class="button" onclick="nextStep(3)">
              Next
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-left:6px">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </button>
          </div>
        </div>
        </div>
      </div>

      <!-- Step 3: Campaign Timeline -->
      <div class="form-step" id="step3">
        <div class="panel form-section" style="animation-delay: 0.3s">
          <span class="section-indicator active">Step 3 of 4</span>
          <h3 style="margin-top:0;margin-bottom:1rem;font-size:1.2rem;font-weight:600;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Campaign Timeline
          </h3>
        
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:1rem">
          <div style="flex:1;min-width:250px">
            <label for="start_date">Start Date <span style="color:#e74c3c;">*</span></label>
            <input 
              id="start_date" 
              name="start_date" 
              type="date" 
              value="{{ old('start_date') }}" 
              required 
              min="{{ date('Y-m-d') }}"
            >
            <div class="muted" style="margin-top:6px;font-size:0.85rem">When will your campaign begin accepting donations?</div>
          </div>

          <div style="flex:1;min-width:250px">
            <label for="end_date">End Date <span style="color:#999;">Optional</span></label>
            <input 
              id="end_date" 
              name="end_date" 
              type="date" 
              value="{{ old('end_date') }}" 
              min="{{ date('Y-m-d') }}"
            >
            <div class="muted" style="margin-top:6px;font-size:0.85rem">Set a deadline to create urgency (optional)</div>
          </div>
        </div>

        <div class="estimated-timeline" id="timelineEstimate" style="display:none">
          <strong>📅 Campaign Duration:</strong> <span id="durationText">Not set</span>
        </div>

        <div class="step-navigation">
          <div class="step-nav-left">
            <button type="button" class="button button-outline" onclick="previousStep(2)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px">
                <polyline points="15 18 9 12 15 6"></polyline>
              </svg>
              Back
            </button>
            <button type="button" class="button button-outline" id="saveDraftBtn3" onclick="saveDraft()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              Save Draft
            </button>
          </div>
          <div class="step-nav-right">
            <a class="button button-outline" href="{{ route('campaigns.index') }}">Cancel</a>
            <button type="button" class="button" onclick="nextStep(4)">
              Next
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-left:6px">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </button>
          </div>
        </div>
        </div>
      </div>

      <!-- Step 4: Review & Submit -->
      <div class="form-step" id="step4">
        <!-- Review Summary -->
        <div class="panel form-section" style="animation-delay: 0.4s">
          <span class="section-indicator active">Step 4 of 4</span>
          <h3 style="margin-top:0;margin-bottom:1rem;font-size:1.2rem;font-weight:600;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:8px">
              <path d="M9 11l3 3L22 4"></path>
              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
            </svg>
            Review Your Campaign
          </h3>
          
          <p class="muted" style="margin-bottom:1.5rem">Please review all the details below before creating your campaign. You can go back to edit any section.</p>

          <!-- Campaign Title -->
          <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #e9ecef">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px">
              <strong style="color:#6c757d;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.5px">Campaign Title</strong>
              <button type="button" onclick="nextStep(1)" class="button button-outline" style="padding:6px 14px;font-size:0.85rem;display:flex;align-items:center;gap:6px;transition:all 0.2s">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
              </button>
            </div>
            <p id="reviewTitle" style="margin:0;font-size:1.1rem;font-weight:600;color:#2c3e50">Not provided</p>
          </div>

          <!-- Description -->
          <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #e9ecef">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px">
              <strong style="color:#6c757d;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.5px">Description</strong>
              <button type="button" onclick="nextStep(1)" class="button button-outline" style="padding:6px 14px;font-size:0.85rem;display:flex;align-items:center;gap:6px;transition:all 0.2s">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
              </button>
            </div>
            <p id="reviewDescription" style="margin:0;white-space:pre-wrap;line-height:1.6;color:#495057">Not provided</p>
          </div>

          <!-- Funding Goal -->
          <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #e9ecef">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px">
              <strong style="color:#6c757d;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.5px">Funding Goal</strong>
              <button type="button" onclick="nextStep(2)" class="button button-outline" style="padding:6px 14px;font-size:0.85rem;display:flex;align-items:center;gap:6px;transition:all 0.2s">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
              </button>
            </div>
            <p id="reviewGoal" style="margin:0;font-size:1.5rem;font-weight:700;color:#3498db">€0.00</p>
          </div>

          <!-- Category -->
          <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #e9ecef">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px">
              <strong style="color:#6c757d;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.5px">Category</strong>
              <button type="button" onclick="nextStep(2)" class="button button-outline" style="padding:6px 14px;font-size:0.85rem;display:flex;align-items:center;gap:6px;transition:all 0.2s">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
              </button>
            </div>
            <p id="reviewCategory" style="margin:0;font-size:1rem;color:#495057">Not selected</p>
          </div>

          <!-- Campaign Dates -->
          <div style="margin-bottom:1.5rem;padding-bottom:1.5rem;border-bottom:1px solid #e9ecef">
            <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:8px">
              <strong style="color:#6c757d;font-size:0.9rem;text-transform:uppercase;letter-spacing:0.5px">Campaign Timeline</strong>
              <button type="button" onclick="nextStep(3)" class="button button-outline" style="padding:6px 14px;font-size:0.85rem;display:flex;align-items:center;gap:6px;transition:all 0.2s">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                  <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                Edit
              </button>
            </div>
            <div style="display:flex;gap:2rem;flex-wrap:wrap">
              <div>
                <div class="muted" style="font-size:0.85rem;margin-bottom:4px">Start Date</div>
                <p id="reviewStartDate" style="margin:0;font-weight:600;color:#495057">Not set</p>
              </div>
              <div>
                <div class="muted" style="font-size:0.85rem;margin-bottom:4px">End Date</div>
                <p id="reviewEndDate" style="margin:0;font-weight:600;color:#495057">No deadline</p>
              </div>
              <div id="reviewDurationContainer" style="display:none">
                <div class="muted" style="font-size:0.85rem;margin-bottom:4px">Duration</div>
                <p id="reviewDuration" style="margin:0;font-weight:600;color:#3498db">0 days</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Tips Panel -->
        <div class="panel form-section" style="background-color:#f8f9fa;border:1px solid #e9ecef;margin-top:1rem;animation-delay: 0.5s">
          <div style="display:flex;align-items:start;gap:12px">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3498db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px">
              <path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <div>
              <strong style="display:block;margin-bottom:8px;font-size:1.05rem">💡 Tips for a successful campaign</strong>
              <ul style="margin:0;padding-left:1.25rem;line-height:1.8">
                <li>Use a <strong>clear, specific title</strong> that explains your goal</li>
                <li>Write a <strong>detailed description</strong> (aim for 500+ words)</li>
                <li>Set a <strong>realistic goal</strong> based on your actual needs</li>
                <li>Add regular <strong>updates</strong> after launch to keep supporters engaged</li>
                <li>Share your campaign on <strong>social media</strong> and with your network</li>
                <li>Respond to <strong>comments and questions</strong> promptly</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Form Completion Indicator -->
        <div class="panel form-section" style="margin-top:1rem;animation-delay: 0.6s">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <div>
              <strong style="font-size:1.05rem">Form Completion</strong>
              <div class="muted" style="font-size:0.85rem;margin-top:4px">Fill in all required fields to create your campaign</div>
            </div>
            <div style="text-align:right">
              <div style="font-size:2rem;font-weight:700;color:#3498db" id="completionPercentage">0%</div>
              <div class="muted" style="font-size:0.85rem">Complete</div>
            </div>
          </div>
          <div style="height:8px;background:#e9ecef;border-radius:4px;overflow:hidden">
            <div id="completionBar" style="height:100%;background:linear-gradient(90deg, #3498db, #2ecc71);width:0%;transition:width 0.3s ease"></div>
          </div>
        </div>

        <!-- Submit Buttons -->
        <div class="step-navigation">
          <div class="step-nav-left">
            <button type="button" class="button button-outline" onclick="previousStep(3)">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px">
                <polyline points="15 18 9 12 15 6"></polyline>
              </svg>
              Back
            </button>
            <button type="button" class="button button-outline" id="saveDraftBtn4" onclick="saveDraft()">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              Save Draft
            </button>
          </div>
          <div class="step-nav-right">
            <a class="button button-outline" href="{{ route('campaigns.index') }}">Cancel</a>
            <button class="button-create-campaign" type="submit" id="submitBtn" disabled>
              <span class="button-create-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
              </span>
              <span class="button-create-text">Launch Campaign</span>
              <span class="button-create-shine"></span>
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
(function() {
  const form = document.getElementById('campaignForm');
  const titleInput = document.getElementById('title');
  const descInput = document.getElementById('description');
  const goalInput = document.getElementById('goal_amount');
  const categoryInput = document.getElementById('category_id');
  const startDateInput = document.getElementById('start_date');
  const endDateInput = document.getElementById('end_date');
  const submitBtn = document.getElementById('submitBtn');

  // Step navigation
  window.currentStep = 1;

  window.nextStep = function(step) {
    document.getElementById('step' + currentStep).classList.remove('active');
    document.getElementById('step' + step).classList.add('active');
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    updateStepProgress();
  };

  window.previousStep = function(step) {
    document.getElementById('step' + currentStep).classList.remove('active');
    document.getElementById('step' + step).classList.add('active');
    currentStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
    updateStepProgress();
  };

  function updateStepProgress() {
      const steps = document.querySelectorAll('.progress-step');
      
      steps.forEach((s) => {
          const stepNum = parseInt(s.getAttribute('data-step'));
          // Set the blue "Active" state based on the current page
          if (stepNum === currentStep) {
              s.classList.add('active');
          } else {
              s.classList.remove('active');
          }
      });

      if (currentStep === 4) {
          updateReviewSection();
      }
  }

  function updateReviewSection() {
    // Title
    const title = titleInput.value.trim();
    document.getElementById('reviewTitle').textContent = title || 'Not provided';

    // Description
    const description = descInput.value.trim();
    document.getElementById('reviewDescription').textContent = description || 'Not provided';

    // Goal
    const goal = parseFloat(goalInput.value);
    if (goal > 0) {
      document.getElementById('reviewGoal').textContent = '€' + goal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } else {
      document.getElementById('reviewGoal').textContent = '€0.00';
    }

    // Category
    const categorySelect = categoryInput;
    const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text;
    document.getElementById('reviewCategory').textContent = categoryText || 'Not selected';

    // Dates
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    
    if (startDate) {
      const startDateObj = new Date(startDate);
      document.getElementById('reviewStartDate').textContent = startDateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    } else {
      document.getElementById('reviewStartDate').textContent = 'Not set';
    }

    if (endDate) {
      const endDateObj = new Date(endDate);
      document.getElementById('reviewEndDate').textContent = endDateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      
      // Calculate duration
      if (startDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        document.getElementById('reviewDuration').textContent = days + ' day' + (days !== 1 ? 's' : '');
        document.getElementById('reviewDurationContainer').style.display = 'block';
      }
    } else {
      document.getElementById('reviewEndDate').textContent = 'No deadline';
      document.getElementById('reviewDurationContainer').style.display = 'none';
    }
  }

  // Character counters
  titleInput.addEventListener('input', function() {
    const count = this.value.length;
    const counter = document.getElementById('titleCounter');
    counter.textContent = count + ' / 255';
    counter.className = 'char-counter';
    if (count > 230) counter.classList.add('warning');
    if (count > 250) counter.classList.add('danger');
    
    // Validation indicator
    const wrapper = this.closest('.input-wrapper');
    if (count >= 10 && count <= 255) {
      wrapper.classList.add('valid');
      wrapper.classList.remove('invalid');
    } else if (count > 0) {
      wrapper.classList.add('invalid');
      wrapper.classList.remove('valid');
    } else {
      wrapper.classList.remove('valid', 'invalid');
    }
    
    updateProgress();
  });

  descInput.addEventListener('input', function() {
    const count = this.value.length;
    const counter = document.getElementById('descCounter');
    counter.textContent = count + ' character' + (count !== 1 ? 's' : '');
    counter.className = 'char-counter';
    if (count < 200 && count > 0) counter.classList.add('warning');
    updateProgress();
  });

  // Goal amount preview
  goalInput.addEventListener('input', function() {
    const value = parseFloat(this.value);
    const preview = document.getElementById('goalPreview');
    const amountEl = document.getElementById('goalAmount');
    
    if (value > 0) {
      preview.style.display = 'block';
      amountEl.textContent = '€' + value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    } else {
      preview.style.display = 'none';
    }
    updateProgress();
  });

  categoryInput.addEventListener('change', updateProgress);

  // Date handling
  startDateInput.addEventListener('change', function() {
    endDateInput.min = this.value || '{{ date('Y-m-d') }}';
    updateTimeline();
    updateProgress();
  });

  endDateInput.addEventListener('change', function() {
    updateTimeline();
    updateProgress();
  });

  function updateTimeline() {
    const start = new Date(startDateInput.value);
    const end = endDateInput.value ? new Date(endDateInput.value) : null;
    const estimate = document.getElementById('timelineEstimate');
    const durationText = document.getElementById('durationText');

    if (end && start <= end) {
      const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
      estimate.style.display = 'block';
      durationText.textContent = days + ' day' + (days !== 1 ? 's' : '') + ' (' + start.toLocaleDateString() + ' - ' + end.toLocaleDateString() + ')';
    } else if (startDateInput.value) {
      estimate.style.display = 'block';
      durationText.textContent = 'No end date (open-ended campaign from ' + start.toLocaleDateString() + ')';
    } else {
      estimate.style.display = 'none';
    }
  }

  function updateProgress() {
      // 1. Define specific validation for each step's requirements
      const stepValidations = {
          1: () => titleInput.value.trim().length >= 10 && descInput.value.trim().length >= 50,
          2: () => parseFloat(goalInput.value) > 0 && categoryInput.value !== '',
          3: () => startDateInput.value !== ''
      };

      let validStepsCount = 0;
      
      // 2. Loop through the first 3 steps to update their green "Completed" state
      for (let i = 1; i <= 3; i++) {
          const stepElement = document.querySelector(`.progress-step[data-step="${i}"]`);
          if (stepValidations[i]()) {
              stepElement.classList.add('completed');
              validStepsCount++;
          } else {
              stepElement.classList.remove('completed');
          }
      }

      // 3. Update the Review step (Step 4) only if all previous are valid
      const allValid = validStepsCount === 3;
      const step4Element = document.querySelector('.progress-step[data-step="4"]');
      if (allValid) {
          step4Element.classList.add('completed');
      } else {
          step4Element.classList.remove('completed');
      }

      // 4. Update the visual Progress Bar (the line) based on valid steps
      const progressBar = document.getElementById('progressBar');
      const progressPercent = (validStepsCount / 3) * 100;
      progressBar.style.width = progressPercent + '%';

      // 5. Update the Form Completion Panel (Bottom)
      const completionPercentage = Math.round((validStepsCount / 3) * 100);
      document.getElementById('completionBar').style.width = completionPercentage + '%';
      document.getElementById('completionPercentage').textContent = completionPercentage + '%';

      // 6. Enable/disable submit button
      submitBtn.disabled = !allValid;
  }

  // Form validation on submit
  form.addEventListener('submit', function(e) {
    const startVal = startDateInput.value;
    const endVal = endDateInput.value;
    
    if (endVal && startVal && endVal < startVal) {
      e.preventDefault();
      alert('Please choose an end date the same as or after the campaign start date.');
      return false;
    }
  });

  // Initialize
  updateProgress();
  updateTimeline();
  loadDraft(); // Load any saved draft
})();

// Save draft to localStorage
function saveDraft() {
  const draftData = {
    title: document.getElementById('title').value,
    description: document.getElementById('description').value,
    goal_amount: document.getElementById('goal_amount').value,
    category_id: document.getElementById('category_id').value,
    start_date: document.getElementById('start_date').value,
    end_date: document.getElementById('end_date').value,
    saved_at: new Date().toISOString()
  };

  localStorage.setItem('campaign_draft', JSON.stringify(draftData));
  
  // Show success message on all save draft buttons
  const buttons = ['saveDraftBtn', 'saveDraftBtn2', 'saveDraftBtn3', 'saveDraftBtn4'];
  buttons.forEach(btnId => {
    const btn = document.getElementById(btnId);
    if (btn) {
      const originalText = btn.innerHTML;
      btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px"><polyline points="20 6 9 17 4 12"></polyline></svg>Draft Saved!';
      btn.style.background = '#2ecc71';
      btn.style.color = 'white';
      btn.style.borderColor = '#2ecc71';
      
      setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = '';
        btn.style.color = '';
        btn.style.borderColor = '';
      }, 2000);
    }
  });
}

// Load draft from localStorage
function loadDraft() {
  const draftJSON = localStorage.getItem('campaign_draft');
  if (!draftJSON) return;

  try {
    const draft = JSON.parse(draftJSON);
    const savedDate = new Date(draft.saved_at);
    const now = new Date();
    const daysDiff = (now - savedDate) / (1000 * 60 * 60 * 24);

    // Only load drafts saved within the last 7 days
    if (daysDiff > 7) {
      localStorage.removeItem('campaign_draft');
      return;
    }

    // Show a notification about the draft
    const notification = document.createElement('div');
    notification.style.cssText = 'position:fixed;top:20px;right:20px;background:#3498db;color:white;padding:16px 20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:10000;max-width:350px;animation:slideIn 0.3s ease';
    notification.innerHTML = `
      <div style="display:flex;align-items:start;gap:12px">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0">
          <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
          <polyline points="17 21 17 13 7 13 7 21"></polyline>
          <polyline points="7 3 7 8 15 8"></polyline>
        </svg>
        <div>
          <strong style="display:block;margin-bottom:4px">Draft Found!</strong>
          <p style="margin:0;font-size:0.9rem;opacity:0.95">We found a draft from ${savedDate.toLocaleDateString()}. Would you like to restore it?</p>
          <div style="margin-top:10px;display:flex;gap:8px">
            <button onclick="restoreDraft()" style="background:white;color:#3498db;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-weight:600;font-size:0.9rem">Restore</button>
            <button onclick="dismissDraft()" style="background:transparent;color:white;border:1px solid white;padding:6px 14px;border-radius:4px;cursor:pointer;font-size:0.9rem">Dismiss</button>
          </div>
        </div>
      </div>
    `;
    
    const style = document.createElement('style');
    style.textContent = '@keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }';
    document.head.appendChild(style);
    
    document.body.appendChild(notification);
    window.draftNotification = notification;
  } catch (e) {
    // Invalid draft data, remove it
    localStorage.removeItem('campaign_draft');
  }
}

// Restore draft data
function restoreDraft() {
  const draftJSON = localStorage.getItem('campaign_draft');
  if (!draftJSON) return;

  try {
    const draft = JSON.parse(draftJSON);
    
    document.getElementById('title').value = draft.title || '';
    document.getElementById('description').value = draft.description || '';
    document.getElementById('goal_amount').value = draft.goal_amount || '';
    document.getElementById('category_id').value = draft.category_id || '';
    document.getElementById('start_date').value = draft.start_date || '';
    document.getElementById('end_date').value = draft.end_date || '';

    // Trigger events to update UI
    document.getElementById('title').dispatchEvent(new Event('input'));
    document.getElementById('description').dispatchEvent(new Event('input'));
    document.getElementById('goal_amount').dispatchEvent(new Event('input'));
    document.getElementById('category_id').dispatchEvent(new Event('change'));
    document.getElementById('start_date').dispatchEvent(new Event('change'));
    document.getElementById('end_date').dispatchEvent(new Event('change'));

    dismissDraft();
  } catch (e) {
    console.error('Error restoring draft:', e);
  }
}

// Dismiss draft notification
function dismissDraft() {
  if (window.draftNotification) {
    window.draftNotification.style.animation = 'slideOut 0.3s ease forwards';
    const style = document.querySelector('style');
    if (style && !style.textContent.includes('slideOut')) {
      style.textContent += '@keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(400px); opacity: 0; } }';
    }
    setTimeout(() => {
      window.draftNotification.remove();
      window.draftNotification = null;
    }, 300);
  }
}

// Clear draft on successful form submission
document.getElementById('campaignForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Trigger celebration effects
  launchCelebration();
  
  // Submit form after a delay to show effects
  setTimeout(() => {
    localStorage.removeItem('campaign_draft');
    e.target.submit();
  }, 2000);
});

// Launch celebration effects
function launchCelebration() {
  // Create overlay
  const overlay = document.createElement('div');
  overlay.className = 'launch-overlay';
  overlay.innerHTML = `
    <div class="launch-content">
      <div class="launch-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
      </div>
      <h2 class="launch-title">🚀 Launching Your Campaign!</h2>
      <p class="launch-message">Your campaign is being created and will be live in moments...</p>
      <div class="launch-spinner"></div>
    </div>
  `;
  document.body.appendChild(overlay);
  
  // Create confetti
  createConfetti();
  
  // Play button animation
  const submitBtn = document.getElementById('submitBtn');
  submitBtn.style.transform = 'scale(0.95)';
  setTimeout(() => {
    submitBtn.style.transform = 'scale(1.1)';
    setTimeout(() => {
      submitBtn.style.transform = 'scale(1)';
    }, 150);
  }, 100);
}

// Create confetti animation
function createConfetti() {
  const colors = ['#0d7db8', '#1aa37a', '#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6', '#1abc9c'];
  const confettiCount = 80;
  
  for (let i = 0; i < confettiCount; i++) {
    setTimeout(() => {
      const confetti = document.createElement('div');
      confetti.className = 'confetti';
      confetti.style.left = Math.random() * 100 + '%';
      confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
      confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
      confetti.style.animationDelay = (Math.random() * 0.5) + 's';
      confetti.style.animation = 'confetti-fall ' + (Math.random() * 2 + 2) + 's ease-in-out forwards';
      
      // Random shapes
      if (Math.random() > 0.5) {
        confetti.style.borderRadius = '50%';
      }
      
      document.body.appendChild(confetti);
      
      // Remove after animation
      setTimeout(() => {
        confetti.remove();
      }, 4000);
    }, i * 20);
  }
}
</script>

@endsection

@extends('layouts.app')

@section('title', 'Edit Campaign')

@section('content')
<div class="profile-shell">
  <header class="campaign-header">
    <h2>Edit Campaign</h2>
    <div class="hero-cta">
      <a href="{{ route('campaigns.show', $campaign->campaign_id) }}" class="button button-outline">Back</a>
    </div>
  </header>

  <div style="max-width:900px;margin:1.25rem auto">
    <?php
        // compute a minimum selectable end date: start_date (allow same-day end)
      $minEnd = null;
      if (!empty($campaign->start_date)) {
          try {
            $minEnd = $campaign->start_date->format('Y-m-d');
          } catch (\Throwable $e) {
              $minEnd = null;
          }
      }
    ?>
    <form method="POST" action="{{ route('campaigns.update', $campaign->campaign_id) }}">
      @csrf
      @method('PATCH')

      @if($errors->any())
        <div class="panel" style="border-left:4px solid #f3c6c6;margin-bottom:12px">
          <ul style="margin:0;padding-left:1.25rem">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="panel">
        <div style="margin-bottom:12px">
          <label for="title">Title</label>
          <input id="title" name="title" type="text" value="{{ old('title', $campaign->title) }}" required>
        </div>

        <div style="margin-bottom:12px">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="6" required>{{ old('description', $campaign->description) }}</textarea>
        </div>

        <div style="display:flex;gap:12px;margin-bottom:12px;flex-wrap:wrap">
          <div style="flex:1;min-width:200px">
            <label for="goal_amount">Goal amount</label>
            <input id="goal_amount" name="goal_amount" type="number" step="0.01" value="{{ old('goal_amount', $campaign->goal_amount) }}" required>
          </div>

          <div style="min-width:200px">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
              @foreach($categories as $cat)
                <option value="{{ $cat->category_id }}" {{ old('category_id', $campaign->category_id) == $cat->category_id ? 'selected' : '' }}>{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap">
          <div style="min-width:200px">
            <label for="end_date">End date</label>
            <input id="end_date" name="end_date" type="date" value="{{ old('end_date', optional($campaign->end_date)->format('Y-m-d')) }}" @if($minEnd) min="{{ $minEnd }}" @endif>
            <div class="muted" style="margin-top:6px">Start date is {{ $campaign->start_date?->format('d M Y') ?? '—' }} and cannot be changed. End dates before the start date are disabled.</div>
          </div>
        </div>
      
      <script>
          // Ensure client-side that the end date cannot be set before the start date.
        (function(){
          var endInput = document.getElementById('end_date');
          if (!endInput) return;
          var form = endInput.closest('form');
          form && form.addEventListener('submit', function(e){
            var val = endInput.value;
            if (!val) return; // empty allowed (nullable)
            // min attribute will be present when start date exists
            var min = endInput.getAttribute('min');
              // allow equality (val === min) but block dates before start (val < min)
              if (min && val < min) {
                e.preventDefault();
                alert('Please choose an end date the same as or after the campaign start date.');
              }
          });
        })();
      </script>

        <div style="margin-top:12px;display:flex;gap:12px">
          <button class="button" type="submit">Save changes</button>
          <a class="button button-outline" href="{{ route('campaigns.show', $campaign->campaign_id) }}">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@extends('layouts.portal')
@section('title', 'Login')

@section('content')
<div class="ptl-card" style="max-width: 440px; margin: 0 auto;">
  <p style="text-align: center; color: #5c5550; font-size: 14px; margin-bottom: 24px;">Enter your Booking Reference and Phone Number to access your event dashboard.</p>

  <form action="{{ route('portal.login.post') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
    @csrf
    <div>
      <label class="ptl-label">Booking Reference (e.g. PLAN-2026-001)</label>
      <input type="text" name="plan_ref" value="{{ old('plan_ref') }}" class="ptl-input" placeholder="PLAN-YYYY-XXX" required>
    </div>

    <div>
      <label class="ptl-label">Phone Number (2547XXXXXXXX)</label>
      <input type="text" name="phone" value="{{ old('phone') }}" class="ptl-input" placeholder="2547XXXXXXXX" required>
    </div>

    <button type="submit" class="ptl-btn-primary">
      Access My Booking
    </button>
  </form>

  <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #f0ece8; text-align: center; display: flex; flex-direction: column; gap: 12px;">
    <span style="font-size: 13px; color: #a09890; text-transform: uppercase; letter-spacing: 0.05em;">New Customer?</span>
    <a href="{{ route('portal.book') }}" 
       style="display: flex; align-items: center; justify-content: center; height: 48px; border: 1.5px solid #CC0000; color: #CC0000; text-decoration: none; font-weight: 700; font-size: 14px; border-radius: 8px; transition: all 0.2s;"
       onmouseover="this.style.background='#fff0f0'"
       onmouseout="this.style.background='transparent'">
        REQUEST EQUIPMENT BOOKING
    </a>
  </div>

  <div style="margin-top: 16px; text-align: center; font-size: 11px; color: #b0a8a0;">
    Need help? Contact us at support@greyapple.co.ke
  </div>
</div>
@endsection

@extends('layouts.app')

@section('title', $module ?? 'Coming Soon')

@section('content')
<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:60vh; text-align:center; padding:40px 20px;">

  <div style="width:64px; height:64px; border-radius:16px; background:#fff0f0; border:1px solid #f5c0c0; display:flex; align-items:center; justify-content:center; margin-bottom:24px;">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <rect x="3" y="3" width="18" height="18" rx="3"/>
      <line x1="12" y1="8" x2="12" y2="12"/>
      <circle cx="12" cy="16" r="0.5" fill="#CC0000"/>
    </svg>
  </div>

  <h1 style="font-family:'Inter',sans-serif; font-size:22px; font-weight:700; color:#1a1a1a; letter-spacing:-0.01em; margin-bottom:8px;">
    {{ $module ?? 'Module' }} — Coming Soon
  </h1>

  <p style="font-family:'Inter',sans-serif; font-size:14px; color:#9a918a; max-width:380px; line-height:1.6; margin-bottom:32px;">
    This module is currently under development and will be available in a future release of the Grey Apple Events Inventory System.
  </p>

  <a href="{{ route('dashboard.index') }}"
     style="display:inline-flex; align-items:center; gap:8px; padding:10px 20px; background:#CC0000; color:#fff; border-radius:8px; text-decoration:none; font-family:'Inter',sans-serif; font-size:13px; font-weight:600; letter-spacing:0.01em; transition:opacity 0.15s;"
     onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
    <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M10 3L5 8l5 5"/>
    </svg>
    Back to Dashboard
  </a>

</div>
@endsection

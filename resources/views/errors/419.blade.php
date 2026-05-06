@extends('layouts.auth')

@section('title', 'Session Expired')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600;700&display=swap" rel="stylesheet">

<div style="width:100vw; min-height:100vh; background:#EEEBE6; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:2rem;">

    {{-- Lock icon --}}
    <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="#CC0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:1.5rem;">
        <rect x="3" y="11" width="18" height="11" rx="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    </svg>

    {{-- Badge --}}
    <div style="display:inline-flex; align-items:center; gap:8px; background:#1a1a1a; color:#fff; font-size:11px; text-transform:uppercase; letter-spacing:0.12em; padding:6px 14px; border-radius:999px; margin-bottom:1.75rem;">
        <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#CC0000; animation:blink-pulse 1.4s ease-in-out infinite;"></span>
        Grey Apple Events
    </div>

    {{-- Heading --}}
    <h1 style="font-family:'Oswald',sans-serif; font-weight:700; font-size:clamp(28px,4vw,46px); color:#0f0f0f; text-transform:uppercase; letter-spacing:-0.5px; line-height:1; margin-bottom:1rem; text-align:center;">
        Session Expired
    </h1>

    {{-- Subtext --}}
    <p style="font-size:14px; color:#888; text-align:center; max-width:340px; line-height:1.7; margin-bottom:2rem;">
        Your session has expired for security reasons.<br>Please return to the login page.
    </p>

    {{-- Button --}}
    <a href="{{ route('login') }}"
       style="display:inline-block; background:#CC0000; color:#fff; font-family:'Oswald',sans-serif; font-weight:700; font-size:13px; text-transform:uppercase; letter-spacing:0.18em; padding:0 2.5rem; height:52px; line-height:52px; border-radius:2px; text-decoration:none; transition:background 0.2s;"
       onmouseover="this.style.background='#aa0000'"
       onmouseout="this.style.background='#CC0000'">
        Back to Login
    </a>

    {{-- Copyright --}}
    <p style="margin-top:3rem; font-size:11px; color:#aaa; text-align:center;">
        &copy; 2026 Grey Apple Events. All rights reserved.
    </p>

</div>

<style>
    @keyframes blink-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(204,0,0,0.7); opacity: 1; }
        50%  { box-shadow: 0 0 0 6px rgba(204,0,0,0); opacity: 0.3; }
        100% { box-shadow: 0 0 0 0 rgba(204,0,0,0); opacity: 1; }
    }
</style>

@endsection

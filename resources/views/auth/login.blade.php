@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<style>
    .login-wrapper {
        position: relative;
        width: 100vw;
        min-height: 100vh;
        overflow: hidden;
        background-image: url('{{ asset('images/Login.png') }}');
        background-size: cover;
        background-position: left center;
    }

    .form-panel {
        position: absolute;
        top: 0;
        right: 0;
        left: auto;
        width: 46%;
        min-height: 100vh;
        height: 100%;
        background-color: #EEEBE6;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        padding: 0 2.5rem 0 3.5rem;
    }

    .form-inner {
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
    }

    .login-heading {
        font-family: 'Oswald', sans-serif;
        font-weight: 800;
        font-size: clamp(32px, 3.5vw, 52px);
        line-height: 0.9;
        color: #0f0f0f;
        text-transform: uppercase;
        letter-spacing: -1px;
        margin-bottom: 0.5rem;
    }

    .login-input {
        width: 100%;
        height: 48px;
        background: #fff;
        border: 1.5px solid #d0d0d0;
        border-radius: 2px;
        padding: 0 14px;
        font-size: 14px;
        color: #0f0f0f;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, border-left 0.1s;
        box-sizing: border-box;
    }

    .login-input:focus {
        border-color: #CC0000;
        border-left: 3px solid #CC0000;
        box-shadow: 0 0 0 3px rgba(204, 0, 0, 0.10);
    }

    .login-btn {
        width: 100%;
        height: 52px;
        background: #CC0000;
        color: #fff;
        font-family: 'Oswald', sans-serif;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        border: none;
        border-radius: 2px;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
    }

    .login-btn:hover {
        background: #aa0000;
    }

    /* Badge sensor dot */
    .badge-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #CC0000;
        flex-shrink: 0;
        animation: blink-pulse 1.4s ease-in-out infinite;
    }

    @keyframes blink-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(204,0,0,0.7); opacity: 1; }
        50%  { box-shadow: 0 0 0 6px rgba(204,0,0,0); opacity: 0.3; }
        100% { box-shadow: 0 0 0 0 rgba(204,0,0,0); opacity: 1; }
    }

    /* Typewriter cursor */
    .cursor {
        display: inline-block;
        width: 2px;
        height: 1em;
        background: #CC0000;
        margin-left: 2px;
        vertical-align: middle;
        animation: cur-blink 0.8s step-end infinite;
    }

    @keyframes cur-blink {
        0%, 100% { opacity: 1; }
        50%      { opacity: 0; }
    }

    @keyframes gaSpin {
      from { transform: rotate(0deg); }
      to   { transform: rotate(360deg); }
    }
    #signin-spinner {
      animation: gaSpin 0.8s linear infinite;
    }

    @media (max-width: 767px) {
        .login-wrapper {
            background-image: none;
        }

        .form-panel {
            position: relative;
            width: 100%;
            min-height: 100vh;
            height: auto;
            padding: 3rem 2rem;
        }

        .form-inner {
            max-width: 100%;
        }
    }
</style>

<div class="login-wrapper">
    <div class="form-panel">
        <div class="form-inner">

            {{-- Pill badge --}}
            <div style="display:inline-flex; align-items:center; gap:8px; background:#1a1a1a; color:#fff; font-size:11px; text-transform:uppercase; letter-spacing:0.12em; padding:6px 14px; border-radius:999px; margin-bottom:1.5rem;">
                <span class="badge-dot"></span>
                GREY APPLE EVENTS LIMITED
            </div>

            {{-- Heading --}}
            <div class="login-heading">
                Inventory<br>Management<br>System
            </div>

            {{-- Sign in label --}}
            <p style="font-size:13px; color:#CC0000; letter-spacing:0.15em; text-transform:uppercase; margin-bottom:1.25rem; font-family:'Oswald',sans-serif; font-weight:600;"><span id="typewriter-text"></span><span class="cursor"></span></p>

            {{-- Scan redirect notice --}}
            @if(session('scan_redirect'))
            <div class="auth-scan-notice">
                <span style="font-size: 16px;">🔐</span>
                <span>Sign in to access the scan session for <strong>{{ session('event_name') }}</strong></span>
            </div>
            @endif

            {{-- Validation errors --}}
            @if($errors->any())
                <div style="background:#fee2e2; border:1px solid #f87171; color:#b91c1c; padding:12px 16px; border-radius:4px; margin-bottom:1.25rem;">
                    <ul style="list-style:disc; padding-left:1.2rem; font-size:13px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div style="margin-bottom:0.75rem;">
                    <label style="display:block; font-size:11px; text-transform:uppercase; color:#999; letter-spacing:0.1em; margin-bottom:6px;">Enterprise Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="login-input">
                </div>

                {{-- Password --}}
                <div style="margin-bottom:0.75rem;">
                    <label style="display:block; font-size:11px; text-transform:uppercase; color:#999; letter-spacing:0.1em; margin-bottom:6px;">Password</label>
                    <input type="password" name="password" required class="login-input">
                </div>

                {{-- Remember me --}}
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:1.5rem;">
                    <input type="checkbox" name="remember" id="remember" style="accent-color:#CC0000; width:15px; height:15px;">
                    <label for="remember" style="font-size:13px; color:#888; cursor:pointer;">Keep me signed in</label>
                </div>

                {{-- Submit --}}
                <button type="submit" id="signin-btn"
                        class="w-full font-bold uppercase tracking-widest text-white transition-all"
                        style="height:52px; background:#CC0000; border:none; border-radius:4px; font-size:13px; letter-spacing:0.18em; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px;"
                        onmouseover="if(!this.disabled)this.style.background='#aa0000'"
                        onmouseout="if(!this.disabled)this.style.background='#CC0000'">
                  <span id="signin-btn-text" style="letter-spacing:0.18em;">SIGN IN</span>
                  <span id="signin-btn-loading" style="display:none; align-items:center; gap:8px;">
                    <svg id="signin-spinner" width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                      <path d="M8 1.5a6.5 6.5 0 1 1-4.6 1.9"/>
                    </svg>
                    Signing in...
                  </span>
                </button>

                <div style="margin-top: 1.5rem; text-align: center; display: flex; flex-direction: column; gap: 12px;">
                    <div style="height: 1px; background: #e0dcd8; width: 100%;"></div>
                    <span style="font-size:12px; color:#999; text-transform: uppercase; letter-spacing: 0.1em;">New to Grey Apple?</span>
                    <a href="{{ route('register') }}" 
                       style="display: flex; align-items: center; justify-content: center; height: 48px; border: 1.5px solid #CC0000; color: #CC0000; text-decoration: none; font-family: 'Oswald', sans-serif; font-weight: 600; font-size: 13px; letter-spacing: 0.1em; border-radius: 2px; transition: all 0.2s;"
                       onmouseover="this.style.background='#fff0f0'"
                       onmouseout="this.style.background='transparent'">
                        CREATE AN ACCOUNT
                    </a>
                </div>

            </form>

            {{-- Copyright --}}
            <p style="text-align:center; font-size:11px; color:#aaa; margin-top:2rem;">
                &copy; 2026 Grey Apple Events. All rights reserved.
            </p>

        </div>
    </div>
</div>

<script>
(function(){
    const TEXT = "Sign In";
    const TYPE_MS = 150;
    const ERASE_MS = 70;
    const HOLD_MS = 2500;
    const GAP_MS = 500;
    const el = document.getElementById('typewriter-text');
    let timeout;
    function type(i){
        el.textContent = TEXT.slice(0, i);
        if(i < TEXT.length) timeout = setTimeout(() => type(i+1), TYPE_MS);
        else timeout = setTimeout(() => erase(TEXT.length), HOLD_MS);
    }
    function erase(i){
        el.textContent = TEXT.slice(0, i);
        if(i > 0) timeout = setTimeout(() => erase(i-1), ERASE_MS);
        else timeout = setTimeout(() => type(1), GAP_MS);
    }
    setTimeout(() => type(1), 400);
})();

document.querySelector('form').addEventListener('submit', function() {
  var btn     = document.getElementById('signin-btn');
  var text    = document.getElementById('signin-btn-text');
  var loading = document.getElementById('signin-btn-loading');
  if (!btn) return;
  btn.disabled = true;
  btn.style.background = '#aa0000';
  btn.style.cursor = 'not-allowed';
  text.style.display = 'none';
  loading.style.display = 'flex';
});

window.addEventListener('pageshow', function(e) {
  if (e.persisted) {
    var btn     = document.getElementById('signin-btn');
    var text    = document.getElementById('signin-btn-text');
    var loading = document.getElementById('signin-btn-loading');
    if (!btn) return;
    btn.disabled = false;
    btn.style.background = '#CC0000';
    btn.style.cursor = 'pointer';
    text.style.display = 'inline';
    loading.style.display = 'none';
  }
});
</script>

@endsection

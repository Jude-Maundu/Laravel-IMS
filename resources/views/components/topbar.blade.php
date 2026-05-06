@php
$nameParts = explode(' ', auth()->user()->name ?? '');
$initials = strtoupper(substr($nameParts[0] ?? '', 0, 1) . substr(end($nameParts), 0, 1));
@endphp

<header id="ga-topbar">

    {{-- Section A: Page identity --}}
    <div style="flex:1; min-width:0;">
        <p style="font-size:15px; font-weight:600; color:#0f0f0f; margin:0; line-height:1.2; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">@yield('page-title', 'Dashboard')</p>
        <p style="font-size:10px; color:#bbb; margin:0; letter-spacing:0.02em;">Grey Apple Events · Inventory Management</p>
    </div>

    {{-- Section B: Search bar --}}
    <div style="display:flex; align-items:center; gap:8px; background:#f5f2ee; border:0.5px solid #e0dcd6; border-radius:8px; padding:0 12px; height:32px; width:210px; flex-shrink:0; cursor:text;">
        <svg viewBox="0 0 16 16" width="13" height="13" fill="none" stroke="#bbb" stroke-width="1.5" stroke-linecap="round">
            <circle cx="7" cy="7" r="4.5"/>
            <line x1="10.5" y1="10.5" x2="14" y2="14"/>
        </svg>
        <span style="font-size:12px; color:#bbb;">Search items...</span>
    </div>

    {{-- Section C: Vertical divider --}}
    <div style="width:0.5px; height:22px; background:#e8e5e0; flex-shrink:0;"></div>

    {{-- Section D: Icon buttons --}}
    <div style="display:flex; align-items:center; gap:4px;">

        {{-- Button 1: Notifications --}}
        <div style="width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; position:relative; border:0.5px solid transparent;"
             onmouseover="this.style.background='#f5f2ee'; this.style.borderColor='#e0dcd6';"
             onmouseout="this.style.background=''; this.style.borderColor='transparent';">
            <svg viewBox="0 0 16 16" width="17" height="17" fill="none" stroke="#777" stroke-width="1.5" stroke-linecap="round">
                <path d="M8 1a4.5 4.5 0 0 1 4.5 4.5V9l1.5 2.5H2L3.5 9V5.5A4.5 4.5 0 0 1 8 1z"/>
                <path d="M6.5 13.5a1.5 1.5 0 0 0 3 0"/>
            </svg>
            <span style="position:absolute; top:7px; right:7px; width:6px; height:6px; border-radius:50%; background:#CC0000; border:1.5px solid #fff;"></span>
        </div>

        {{-- Button 2: Settings --}}
        <div style="width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; position:relative; border:0.5px solid transparent;"
             onmouseover="this.style.background='#f5f2ee'; this.style.borderColor='#e0dcd6';"
             onmouseout="this.style.background=''; this.style.borderColor='transparent';">
            <svg viewBox="0 0 16 16" width="17" height="17" fill="none" stroke="#777" stroke-width="1.5" stroke-linecap="round">
                <circle cx="8" cy="8" r="2.5"/>
                <path d="M8 1v2M8 13v2M1 8h2M13 8h2M3.1 3.1l1.4 1.4M11.5 11.5l1.4 1.4M3.1 12.9l1.4-1.4M11.5 4.5l1.4-1.4"/>
            </svg>
        </div>

        {{-- Button 3: Help --}}
        <div style="width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; position:relative; border:0.5px solid transparent;"
             onmouseover="this.style.background='#f5f2ee'; this.style.borderColor='#e0dcd6';"
             onmouseout="this.style.background=''; this.style.borderColor='transparent';">
            <svg viewBox="0 0 16 16" width="17" height="17" fill="none" stroke="#777" stroke-width="1.5" stroke-linecap="round">
                <circle cx="8" cy="8" r="6.5"/>
                <path d="M6 6a2 2 0 1 1 2.5 1.9C8 8.4 8 9 8 9"/>
                <circle cx="8" cy="11.5" r="0.6" fill="#777"/>
            </svg>
        </div>

    </div>

    {{-- Section E: Second vertical divider --}}
    <div style="width:0.5px; height:22px; background:#e8e5e0; flex-shrink:0;"></div>

    {{-- Section F: Role badge --}}
    <span style="background:#0f0f0f; color:#ffffff; font-size:9px; font-weight:600; padding:3px 10px; border-radius:12px; letter-spacing:0.1em; text-transform:uppercase; flex-shrink:0; font-family:'Inter',sans-serif;">{{ strtoupper(auth()->user()->getRoleNames()->first() ?? 'USER') }}</span>

    {{-- Section G: Profile cluster --}}
    <div style="display:flex; align-items:center; gap:9px; padding:4px 8px 4px 4px; border-radius:9px; cursor:pointer; border:0.5px solid transparent; flex-shrink:0;"
         onmouseover="this.style.background='#f5f2ee'; this.style.borderColor='#e0dcd6';"
         onmouseout="this.style.background=''; this.style.borderColor='transparent';">

        {{-- Avatar --}}
        <div style="width:32px; height:32px; border-radius:50%; background:#0f0f0f; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; color:#fff; flex-shrink:0; font-family:'Inter',sans-serif;">
            {{ $initials }}
        </div>

        {{-- Text column --}}
        <div style="display:flex; flex-direction:column;">
            <span style="font-size:13px; font-weight:500; color:#0f0f0f; line-height:1.2;">{{ auth()->user()->name }}</span>
            <span style="font-size:10px; color:#aaa; line-height:1.2;">{{ auth()->user()->email }}</span>
        </div>

        {{-- Chevron --}}
        <svg viewBox="0 0 12 12" width="11" height="11" fill="none" stroke="#bbb" stroke-width="1.5" stroke-linecap="round">
            <path d="M3 4.5l3 3 3-3"/>
        </svg>

    </div>

</header>

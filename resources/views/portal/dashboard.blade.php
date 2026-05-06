@extends('layouts.portal')
@section('title', 'Event Dashboard')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
  <h2 class="ptl-title" style="font-size: 20px;">Welcome, {{ $event->client_name }}</h2>
  <a href="{{ route('portal.logout') }}" style="font-size: 13px; color: #CC0000; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 6px;">
    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
    Logout
  </a>
</div>

<div class="ptl-card">
  <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid #f5f1ed;">
    <div>
      <h3 style="font-size: 18px; margin: 0 0 4px 0; color: #0f0f0f;">{{ $event->name }}</h3>
      <div style="font-size: 13px; color: #5c5550;">
        <span style="font-weight: 600;">Booking ID:</span> {{ $event->plan_ref }}
      </div>
    </div>
    <div style="text-align: right;">
      @if($event->payment_status === 'paid')
        <span class="ptl-badge ptl-badge-success">Paid & Confirmed</span>
      @elseif($event->payment_status === 'pending')
        <span class="ptl-badge ptl-badge-warning">Awaiting Payment</span>
      @else
        <span class="ptl-badge ptl-badge-danger">Payment Failed</span>
      @endif
    </div>
  </div>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
    <div>
      <label class="ptl-label" style="text-transform: uppercase; letter-spacing: 0.5px; font-size: 10px;">Venue</label>
      <div style="font-size: 14px; color: #0f0f0f; font-weight: 500; display: flex; align-items: center; gap: 6px;">
        <svg style="width:16px;height:16px;color:#a09890" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        {{ $event->venue }}
      </div>
    </div>
    <div>
      <label class="ptl-label" style="text-transform: uppercase; letter-spacing: 0.5px; font-size: 10px;">Event Date</label>
      <div style="font-size: 14px; color: #0f0f0f; font-weight: 500; display: flex; align-items: center; gap: 6px;">
        <svg style="width:16px;height:16px;color:#a09890" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        {{ $event->event_date->format('l, jS F Y') }}
      </div>
    </div>
  </div>

  <div style="background: #faf8f6; border: 1px solid #ece8e3; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <div>
        <div style="font-size: 12px; color: #5c5550; font-weight: 600; margin-bottom: 4px;">Total Amount Due</div>
        <div style="font-size: 28px; font-weight: 800; color: #0f0f0f; letter-spacing: -0.5px;">KES {{ number_format($event->amount_due ?? $event->cost ?? 0, 0) }}</div>
      </div>
      
      @if($event->payment_status !== 'paid')
      <button onclick="document.getElementById('payment-modal').style.display='flex'" class="ptl-btn-primary" style="width: auto; padding: 12px 28px;">
        <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        Pay via M-Pesa
      </button>
      @else
      <a href="{{ route('portal.receipt', $event) }}" class="ptl-btn-dark">
        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        Download Receipt
      </a>
      @endif
    </div>
  </div>

  <h4 class="ptl-label" style="margin-bottom: 12px;">Equipment Checklist</h4>
  <div class="db-table-wrap" style="border: 1px solid #ece8e3; border-radius: 10px; overflow: hidden;">
    <table class="db-table">
      <thead>
        <tr>
          <th class="db-th">Item Name</th>
          <th class="db-th" style="text-align: right;">Quantity</th>
        </tr>
      </thead>
      <tbody>
        @foreach($event->eventItems as $ei)
        <tr class="db-tr">
          <td class="db-td" style="font-weight: 600;">{{ $ei->item->name }}</td>
          <td class="db-td" style="text-align: right; font-weight: 700;">{{ $ei->quantity }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Payment Modal --}}
<div id="payment-modal" class="mp-modal-overlay" style="display:none;">
  <div class="mp-modal">
    <button onclick="document.getElementById('payment-modal').style.display='none'" style="position: absolute; top: 16px; right: 16px; background: none; border: none; cursor: pointer; font-size: 24px; color: #a09890;">&times;</button>
    <h3 style="font-family: 'Oswald', sans-serif; margin: 0 0 8px 0; font-size: 20px; color: #0f0f0f; text-transform: uppercase;">M-Pesa Express</h3>
    <p style="font-size: 13px; color: #5c5550; margin-bottom: 24px; line-height: 1.5;">Enter your M-Pesa phone number. We will send an STK Push to your phone requesting payment of <strong>KES {{ number_format($event->amount_due ?? $event->cost ?? 0, 0) }}</strong>.</p>
    
    <form action="{{ route('portal.pay', $event) }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
      @csrf
      <div>
        <label class="ptl-label">Phone Number (2547XXXXXXXX)</label>
        <input type="text" name="phone" value="{{ Session::get('customer_phone') }}" required class="ptl-input" placeholder="2547XXXXXXXX">
      </div>

      <button type="submit" class="mp-btn-stk">
        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/M-PESA_LOGO-01.svg/512px-M-PESA_LOGO-01.svg.png" class="mp-logo" alt="M-Pesa">
        Send STK Push
      </button>
    </form>
    
    <div style="margin-top: 20px; text-align: center;">
        <p style="font-size: 11px; color: #a09890;">Secure payment powered by Daraja API</p>
    </div>
  </div>
</div>

@if($event->payment_status === 'pending' && session('info'))
<script>
  // Simple polling to check for payment success
  setInterval(function() {
    fetch('{{ route("portal.check-status", $event) }}')
      .then(response => response.json())
      .then(data => {
        if (data.payment_status === 'paid') {
          window.location.reload();
        }
      });
  }, 3000);
</script>
@endif

@endsection

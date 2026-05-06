@extends('layouts.app')
@section('title', 'Team Assignment — ' . $event->name)
@section('page-title', 'Events')

@section('content')

{{-- PAGE HEADER --}}
<div class="wiz-page-header">
  <div>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
      <a href="{{ route('events.index') }}" class="wiz-back-link">Events</a>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550">{{ $event->name }}</span>
      <span style="color:#d0c8c0;font-size:12px">/</span>
      <span style="font-size:12px;color:#5c5550;font-weight:500">Team Assignment</span>
    </div>
    <h1 class="wiz-page-title">Assign Team to Event</h1>
  </div>
</div>

{{-- STEP INDICATOR --}}
<div class="wiz-stepper">
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done">
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
    </div>
    <div class="wiz-step-info">
      <span class="wiz-step-label" style="color:#3B6D11">Event Details</span>
      <span class="wiz-step-sub">Completed</span>
    </div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-done">
    <div class="wiz-step-num wiz-step-num-done">
      <svg width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 6l2.5 2.5 5.5-5"/></svg>
    </div>
    <div class="wiz-step-info">
      <span class="wiz-step-label" style="color:#3B6D11">Packing List</span>
      <span class="wiz-step-sub">Completed</span>
    </div>
  </div>
  <div class="wiz-step-line wiz-step-line-done"></div>
  <div class="wiz-step wiz-step-active">
    <div class="wiz-step-num">3</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Assign Team</span>
      <span class="wiz-step-sub">Crew members</span>
    </div>
  </div>
  <div class="wiz-step-line"></div>
  <div class="wiz-step wiz-step-inactive">
    <div class="wiz-step-num">4</div>
    <div class="wiz-step-info">
      <span class="wiz-step-label">Review & Confirm</span>
      <span class="wiz-step-sub">Summary & schedule</span>
    </div>
  </div>
</div>

<div class="wiz-layout">

  {{-- SIDEBAR --}}
  <div class="wiz-sidebar">
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Event</div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Name</span>
        <span class="wiz-sum-val">{{ $event->name }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Client</span>
        <span class="wiz-sum-val">{{ $event->client_name }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Event date</span>
        <span class="wiz-sum-val">{{ $event->event_date->format('d M Y') }}</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Venue</span>
        <span class="wiz-sum-val">{{ $event->venue }}</span>
      </div>
    </div>
    <div class="wiz-sidebar-card">
      <div class="wiz-sidebar-title">Team</div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Assigned</span>
        <span class="wiz-sum-val" id="team-count" style="color:#CC0000;font-weight:700">{{ $assignedStaff->count() }} staff</span>
      </div>
      <div class="wiz-summary-item">
        <span class="wiz-sum-label">Team leader</span>
        <span class="wiz-sum-val" id="leader-name" style="color:#3B6D11">
          @php $leader = $assignedStaff->firstWhere('pivot.role', 'leader'); @endphp
          {{ $leader ? $leader->name : 'Not set' }}
        </span>
      </div>
    </div>
  </div>

  {{-- MAIN --}}
  <div class="wiz-main">
    <div class="wiz-card">
      <div class="wiz-card-head">
        <div>
          <div class="wiz-card-title">Team Assignment</div>
          <div class="wiz-card-sub">Search and add staff members to this event. Star one person as team leader.</div>
        </div>
      </div>
      <div class="wiz-card-body">

        {{-- TEAM DROPDOWN --}}
        <div class="evt-dropdown-container">
          <label for="team-dropdown" class="evt-dropdown-label">Select staff member to add:</label>
          <select id="team-dropdown" class="evt-dropdown-select">
            <option value="">— Choose a staff member —</option>
            @foreach($availableUsers as $user)
            <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
          </select>
          @if($availableUsers->isEmpty())
          <div style="font-size:11px;color:#a09890;margin-top:8px">All available staff members have been added.</div>
          @endif
        </div>

        {{-- ASSIGNED TEAM --}}
        <div id="assigned-team" class="evt-team-list">
          @foreach($assignedStaff as $staff)
          <div class="evt-team-member" data-user-id="{{ $staff->id }}" data-role="{{ $staff->pivot->role }}">
            <div class="evt-member-avatar">{{ strtoupper(substr($staff->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $staff->name)[1] ?? '', 0, 1)) }}</div>
            <div class="evt-member-info">
              <div class="evt-member-name">{{ $staff->name }}</div>
              <div class="evt-member-email">{{ $staff->email }}</div>
            </div>
            @if($staff->pivot->role === 'leader')
            <button type="button" class="evt-star-btn evt-star-active" onclick="toggleLeader(this)">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" stroke="none"><path d="M8 1l2 5h5l-4 3.5 1.5 5L8 11l-4.5 3.5 1.5-5L1 6h5z"/></svg>
            </button>
            @else
            <button type="button" class="evt-star-btn" onclick="toggleLeader(this)">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 1l2 5h5l-4 3.5 1.5 5L8 11l-4.5 3.5 1.5-5L1 6h5z"/></svg>
            </button>
            @endif
            <button type="button" class="evt-remove-btn" onclick="removeMember(this)">
              <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><line x1="3" y1="3" x2="13" y2="13"/><line x1="13" y1="3" x2="3" y2="13"/></svg>
            </button>
          </div>
          @endforeach
        </div>

      </div>
      <div class="wiz-card-footer">
        <span class="wiz-footer-hint">Step 3 of 4 &mdash; Next: review and confirm</span>
        <div class="wiz-footer-actions">
          <a href="{{ route('events.checklist', $event) }}" class="wiz-btn-cancel">← Back</a>
          <button type="button" class="wiz-btn-next" id="save-team-btn" onclick="saveTeam()">
            Continue to Review
            <svg width="13" height="13" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
(function() {
  const dropdown = document.getElementById('team-dropdown');
  const assignedTeam = document.getElementById('assigned-team');
  const eventId = {{ $event->id }};
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  // Handle dropdown selection
  dropdown.addEventListener('change', function() {
    if (!this.value) return;

    const userId = parseInt(this.value);
    const option = this.options[this.selectedIndex];
    const userName = option.getAttribute('data-name');
    const userEmail = option.getAttribute('data-email');

    addMember(userId, userName, userEmail);

    // Reset dropdown
    this.value = '';

    // Remove the selected option from dropdown (already added)
    option.style.display = 'none';
  });

  window.addMember = function(userId, userName, userEmail) {
    const exists = document.querySelector(`.evt-team-member[data-user-id="${userId}"]`);
    if (exists) return;

    const initials = (userName.split(' ').map(n => n[0]).join('').substring(0, 2)).toUpperCase();

    const memberHtml = `
      <div class="evt-team-member" data-user-id="${userId}" data-role="member">
        <div class="evt-member-avatar">${initials}</div>
        <div class="evt-member-info">
          <div class="evt-member-name">${userName}</div>
          <div class="evt-member-email">${userEmail}</div>
        </div>
        <button type="button" class="evt-star-btn" onclick="toggleLeader(this)">
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 1l2 5h5l-4 3.5 1.5 5L8 11l-4.5 3.5 1.5-5L1 6h5z"/></svg>
        </button>
        <button type="button" class="evt-remove-btn" onclick="removeMember(this)">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><line x1="3" y1="3" x2="13" y2="13"/><line x1="13" y1="3" x2="3" y2="13"/></svg>
        </button>
      </div>
    `;

    assignedTeam.insertAdjacentHTML('beforeend', memberHtml);
    updateCounts();
  };

  window.removeMember = function(btn) {
    const member = btn.closest('.evt-team-member');
    const userId = member.getAttribute('data-user-id');

    member.remove();

    // Re-enable the option in dropdown
    const option = document.querySelector(`#team-dropdown option[value="${userId}"]`);
    if (option) {
      option.style.display = '';
    }

    updateCounts();
  };

  window.toggleLeader = function(btn) {
    const member = btn.closest('.evt-team-member');
    const isLeader = member.getAttribute('data-role') === 'leader';

    // Remove leader from all
    document.querySelectorAll('.evt-team-member').forEach(m => {
      m.setAttribute('data-role', 'member');
      const starBtn = m.querySelector('.evt-star-btn');
      starBtn.classList.remove('evt-star-active');
      starBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M8 1l2 5h5l-4 3.5 1.5 5L8 11l-4.5 3.5 1.5-5L1 6h5z"/></svg>';
    });

    if (!isLeader) {
      member.setAttribute('data-role', 'leader');
      btn.classList.add('evt-star-active');
      btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" stroke="none"><path d="M8 1l2 5h5l-4 3.5 1.5 5L8 11l-4.5 3.5 1.5-5L1 6h5z"/></svg>';
    }

    updateCounts();
  };

  function updateCounts() {
    const count = document.querySelectorAll('.evt-team-member').length;
    const leader = document.querySelector('.evt-team-member[data-role="leader"]');
    const leaderName = leader ? leader.querySelector('.evt-member-name').textContent : 'Not set';

    document.getElementById('team-count').textContent = `${count} staff`;
    document.getElementById('leader-name').textContent = leaderName;
    document.getElementById('team-footer-count').textContent = `${count} staff assigned`;
  }

  window.saveTeam = function() {
    const btn = document.getElementById('save-team-btn');
    const members = [];

    document.querySelectorAll('.evt-team-member').forEach(m => {
      members.push({
        user_id: parseInt(m.getAttribute('data-user-id')),
        role: m.getAttribute('data-role')
      });
    });

    btn.disabled = true;
    btn.textContent = 'Saving...';

    fetch(`/events/${eventId}/team`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ team: members })
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        window.location.href = `/events/${eventId}/review`;
      } else {
        alert(data.message || 'Error saving team');
        btn.disabled = false;
        btn.innerHTML = 'Continue to Review <svg width="13" height="13" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>';
      }
    })
    .catch(err => {
      console.error('Save error:', err);
      alert('Error saving team');
      btn.disabled = false;
      btn.innerHTML = 'Continue to Review <svg width="13" height="13" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M4.5 2L7.5 6l-3 4"/></svg>';
    });
  };
})();
</script>

@endsection

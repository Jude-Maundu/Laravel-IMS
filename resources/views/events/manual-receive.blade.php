@extends('layouts.app')
@section('title', 'Manual Receive — ' . $event->name)
@section('page-title', 'Events')

@section('content')

<div class="db-card" style="margin-bottom:24px;">
  <div class="db-card-content">
    <div class="disp-page-header">
      <a href="{{ route('events.show', $event) }}" class="wiz-back-link">
        ← Back to Event
      </a>
      <h1>Manual Receive — {{ $event->name }}</h1>
    </div>
    <div class="disp-warning-banner">
      ℹ Manual receive — enter the last 3 digits of each piece code as items arrive back.
      Use "Mark Missing" for any item that did not return.
    </div>
  </div>
</div>

<form method="POST" action="{{ route('events.receive.manual.store', $event) }}">
    @csrf

    {{-- OWN INVENTORY --}}
    <div class="disp-section-title">Own Inventory</div>

    @foreach($event->eventPieceDispatches->groupBy('itemPiece.item_id') as $itemId => $dispatches)
    @php $item = $dispatches->first()->itemPiece->item; @endphp

    <div class="disp-item-card">
        <div class="disp-item-header">
            @if($item->image_path)
                <img src="{{ asset('storage/' . $item->image_path) }}"
                     class="disp-item-thumb" alt="">
            @endif
            <div>
                <div class="disp-item-name">{{ $item->name }}</div>
                <div class="disp-item-meta">{{ $item->category }} · {{ $dispatches->count() }} pieces dispatched</div>
            </div>
            <div class="disp-pieces-needed">{{ $dispatches->count() }} expected back</div>
        </div>

        {{-- One row per dispatched piece --}}
        @foreach($dispatches as $dispatch)
        @php $piece = $dispatch->itemPiece; @endphp
        <div class="disp-piece-row" id="piece-row-{{ $piece->unique_code }}">
            <span class="disp-piece-prefix">
                {{ substr($piece->unique_code, 0, strrpos($piece->unique_code, '-') + 1) }}
            </span>
            <input type="text"
                   name="received_pieces[{{ $itemId }}][]"
                   value="{{ substr($piece->unique_code, strrpos($piece->unique_code, '-') + 1) }}"
                   class="disp-piece-input"
                   maxlength="3"
                   pattern="[0-9]{3}"
                   data-full-code="{{ $piece->unique_code }}"
                   data-item-id="{{ $itemId }}">
            <span class="disp-piece-valid" id="valid-{{ $piece->unique_code }}"></span>
            <button type="button"
                    onclick="markMissing('{{ $piece->unique_code }}', this)"
                    class="rcv-mark-missing-btn-inline">
                Mark Missing
            </button>
        </div>
        @endforeach

        {{-- Condition --}}
        <div class="disp-condition-wrap">
            <div class="disp-condition-label">Condition on Return</div>
            <div class="disp-condition-btns">
                @foreach([1,2,3,4,5] as $c)
                <button type="button" class="disp-condition-btn"
                        data-item="{{ $itemId }}" data-val="{{ $c }}"
                        onclick="setCondition({{ $itemId }}, {{ $c }}, this)">
                    {{ $c }}
                </button>
                @endforeach
            </div>
            <input type="hidden" name="conditions[{{ $itemId }}]" id="cond-{{ $itemId }}">
        </div>

        {{-- Destination --}}
        <div class="disp-condition-wrap">
            <div class="disp-condition-label">Send To</div>
            <div class="disp-condition-btns">
                <button type="button" class="disp-condition-btn disp-condition-btn-active"
                        onclick="setDest({{ $itemId }}, 'warehouse', this)">
                    🏭 Warehouse
                </button>
                <button type="button" class="disp-condition-btn"
                        onclick="setDest({{ $itemId }}, 'cleaning', this)">
                    🧹 Cleaning
                </button>
                <button type="button" class="disp-condition-btn"
                        onclick="setDest({{ $itemId }}, 'repair', this)">
                    🔧 Repair
                </button>
            </div>
            <input type="hidden" name="destinations[{{ $itemId }}]"
                   id="dest-{{ $itemId }}" value="warehouse">
        </div>

        {{-- Damage note --}}
        <div id="damage-{{ $itemId }}" style="display:none;margin-top:8px">
            <textarea name="damage_notes[{{ $itemId }}]"
                      class="wiz-textarea"
                      placeholder="Describe the damage..."
                      rows="2"></textarea>
        </div>
    </div>
    @endforeach

    {{-- Missing pieces hidden inputs --}}
    <div id="missing-inputs"></div>

    {{-- BORROWED ITEMS --}}
    @if($event->borrowedItems->count() > 0)
    <div class="disp-section-title">Borrowed Items</div>
    @foreach($event->borrowedItems as $borrowed)
    <div class="disp-item-card">
        <div class="disp-item-name">{{ $borrowed->item_name }}</div>
        <div class="disp-item-meta">Source: {{ $borrowed->source_company }}</div>
        <div class="disp-piece-row">
            <label>Quantity returned:</label>
            <input type="number"
                   name="borrowed_returned[{{ $borrowed->id }}]"
                   value="{{ $borrowed->quantity_dispatched }}"
                   min="0"
                   max="{{ $borrowed->quantity_dispatched }}"
                   class="disp-piece-input" style="width:80px">
            <span class="disp-item-meta">of {{ $borrowed->quantity_dispatched }} dispatched</span>
        </div>
    </div>
    @endforeach
    @endif

    {{-- OPERATIONAL ITEMS --}}
    @if($event->operationalItems->count() > 0)
    <div class="disp-section-title">Operational Items</div>
    @foreach($event->operationalItems as $opItem)
    <div class="disp-item-card">
        <div class="disp-item-name">{{ $opItem->display_name }}</div>
        <div class="disp-piece-row">
            <label>Quantity returned:</label>
            <input type="number"
                   name="operational_returned[{{ $opItem->id }}]"
                   value="{{ $opItem->quantity_dispatched }}"
                   min="0"
                   max="{{ $opItem->quantity_dispatched }}"
                   class="disp-piece-input" style="width:80px">
            <span class="disp-item-meta">of {{ $opItem->quantity_dispatched }} dispatched</span>
        </div>
    </div>
    @endforeach
    @endif

    <div class="disp-footer">
        <a href="{{ route('events.show', $event) }}" class="wiz-btn-cancel">Cancel</a>
        <button type="submit" class="wiz-btn-next" style="background:#185FA5">
            Complete Receiving
        </button>
    </div>

</form>

@endsection

@section('scripts')
<script>
function setCondition(itemId, val, btn) {
    document.querySelectorAll('[data-item="' + itemId + '"]').forEach(b => {
        b.classList.remove('disp-condition-btn-active');
    });
    btn.classList.add('disp-condition-btn-active');
    document.getElementById('cond-' + itemId).value = val;
}

function setDest(itemId, dest, btn) {
    btn.closest('.disp-condition-btns').querySelectorAll('.disp-condition-btn')
        .forEach(b => b.classList.remove('disp-condition-btn-active'));
    btn.classList.add('disp-condition-btn-active');
    document.getElementById('dest-' + itemId).value = dest;
    document.getElementById('damage-' + itemId).style.display =
        dest === 'repair' ? 'block' : 'none';
}

function markMissing(uniqueCode, btn) {
    if (!confirm('Mark ' + uniqueCode + ' as missing? It will be removed from the received list.')) return;
    const row = document.getElementById('piece-row-' + uniqueCode);
    if (row) {
        row.style.opacity = '0.4';
        row.querySelectorAll('input').forEach(i => i.disabled = true);
        btn.textContent = '✓ Marked Missing';
        btn.disabled = true;
    }
    // Add hidden input to missing_pieces array
    const hidden = document.createElement('input');
    hidden.type  = 'hidden';
    hidden.name  = 'missing_pieces[]';
    hidden.value = uniqueCode;
    document.getElementById('missing-inputs').appendChild(hidden);
}
</script>
@endsection

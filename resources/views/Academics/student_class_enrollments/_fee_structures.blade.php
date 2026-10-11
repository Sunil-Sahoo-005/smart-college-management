{{-- MULTI-SELECT FEE STRUCTURES --}}
@php
    $selectedFees = !empty($selectedFeeStructureIds ?? [])
        ? $selectedFeeStructureIds
        : (!empty($existingFeeStructureIds ?? []) ? $existingFeeStructureIds : old('fee_structure_ids', []));

    $PREVIEW_LIMIT = 3;

    $cardThemes = [
        ['bg' => 'linear-gradient(145deg, #0f2027, #203a43, #2c5364)', 'dot' => '#38bdf8', 'pill' => 'rgba(56,189,248,0.18)'],
        ['bg' => 'linear-gradient(145deg, #1a1a2e, #16213e, #0f3460)', 'dot' => '#818cf8', 'pill' => 'rgba(129,140,248,0.18)'],
        ['bg' => 'linear-gradient(145deg, #134e4a, #115e59, #0f766e)', 'dot' => '#34d399', 'pill' => 'rgba(52,211,153,0.18)'],
        ['bg' => 'linear-gradient(145deg, #1e1b4b, #312e81, #3730a3)', 'dot' => '#a78bfa', 'pill' => 'rgba(167,139,250,0.18)'],
        ['bg' => 'linear-gradient(145deg, #431407, #7c2d12, #9a3412)', 'dot' => '#fb923c', 'pill' => 'rgba(251,146,60,0.18)'],
    ];
@endphp

@if ($feeStructures->isNotEmpty())
<style>
.fps-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 14px;
    align-items: start;
}
@media (max-width: 1400px) { .fps-grid { grid-template-columns: repeat(4,1fr); } }
@media (max-width: 1100px) { .fps-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 768px)  { .fps-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 480px)  { .fps-grid { grid-template-columns: 1fr; } }

.fps-card {
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
    border: 2px solid transparent;
    background: #fff;
    box-shadow: 0 1px 10px rgba(0,0,0,0.07);
    transition: transform .2s, box-shadow .2s, border-color .15s;
    display: flex;
    flex-direction: column;
}
.fps-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.12); }
.fps-card.fps-selected {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.13), 0 8px 24px rgba(37,99,235,0.14);
}

/* ── Header ── */
.fps-hd {
    padding: 18px 16px 14px;
    position: relative;
    overflow: hidden;
}
.fps-hd-orb {
    position: absolute;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    top: -24px; right: -18px;
    pointer-events: none;
}
.fps-icon {
    width: 34px; height: 34px;
    background: rgba(255,255,255,0.13);
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 10px;
}
.fps-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #fff;
    margin: 0 32px 2px 0;   /* space for toggle */
    line-height: 1.35;
    word-break: break-word;
}
.fps-subtitle {
    font-size: 9.5px;
    font-weight: 600;
    color: rgba(255,255,255,0.5);
    text-transform: uppercase;
    letter-spacing: .07em;
}
.fps-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 12px;
    padding-top: 11px;
    border-top: 1px solid rgba(255,255,255,0.1);
}
.fps-total {
    font-size: 19px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.03em;
    line-height: 1;
}
.fps-total-lbl {
    font-size: 9px;
    color: rgba(255,255,255,0.45);
    text-transform: uppercase;
    letter-spacing: .06em;
}
.fps-items-pill {
    font-size: 9.5px;
    font-weight: 700;
    border-radius: 20px;
    padding: 3px 9px;
    color: rgba(255,255,255,0.85);
    background: rgba(255,255,255,0.13);
    white-space: nowrap;
}

/* ── Toggle ── */
.fps-toggle {
    position: absolute;
    top: 13px; right: 13px;
    z-index: 5;
}
.fps-toggle .form-check-input {
    width: 30px; height: 16px;
    cursor: pointer;
    background-color: rgba(255,255,255,0.16);
    border-color: rgba(255,255,255,0.3);
    transition: all .18s;
}
.fps-toggle .form-check-input:checked {
    background-color: #fff;
    border-color: #fff;
}

/* ── Body ── */
.fps-body { padding: 12px 14px 14px; flex: 1; }

.fps-row {
    display: flex;
    align-items: flex-start;
    gap: 7px;
    padding: 5px 0;
    border-bottom: 1px dashed #f0f1f3;
}
.fps-row:last-child { border-bottom: none; }
.fps-dot {
    width: 5px; height: 5px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 6px;
}
.fps-row-name {
    font-size: 11px;
    color: #6b7280;
    font-weight: 500;
    flex: 1;
    line-height: 1.4;
    word-break: break-word;
}
.fps-row-amt {
    font-size: 11px;
    font-weight: 700;
    color: #1f2937;
    white-space: nowrap;
}

/* +N more button */
.fps-more-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    width: 100%;
    margin-top: 8px;
    padding: 5px 0;
    border: none;
    border-radius: 8px;
    background: #f3f4f6;
    color: #4b5563;
    font-size: 10.5px;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s, color .15s;
    letter-spacing: .02em;
}
.fps-more-btn:hover { background: #e5e7eb; color: #111827; }
.fps-more-btn.fps-expanded { background: #fee2e2; color: #b91c1c; }

/* Hidden rows */
.fps-hidden-rows { display: none; }
.fps-hidden-rows.fps-open { display: block; }

/* Selected stripe */
.fps-stripe {
    display: none;
    background: linear-gradient(90deg, #1d4ed8, #2563eb);
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    text-align: center;
    padding: 4px 0;
    letter-spacing: .09em;
    text-transform: uppercase;
}
.fps-card.fps-selected .fps-stripe { display: block; }

/* Summary bar */
.fps-summary {
    margin-top: 20px;
    border-radius: 14px;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    padding: 18px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    box-shadow: 0 4px 22px rgba(15,23,42,0.2);
}
.fps-summary.fps-hidden { display: none; }
.fps-sum-l { display: flex; align-items: center; gap: 12px; }
.fps-sum-ico {
    width: 42px; height: 42px;
    background: rgba(99,102,241,0.17);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.fps-sum-lbl { font-size: 10px; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: .06em; }
.fps-sum-val { font-size: 17px; font-weight: 800; color: #fff; margin: 0; }
.fps-sum-r { text-align: right; }
.fps-sum-tlbl { font-size: 9.5px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: .06em; }
.fps-sum-tval { font-size: 28px; font-weight: 900; color: #4ade80; letter-spacing: -.04em; line-height: 1; }
</style>

<div class="fps-grid">
@foreach ($feeStructures as $index => $fs)
@php
    $theme    = $cardThemes[$index % count($cardThemes)];
    $isSel    = in_array($fs->id, $selectedFees);
    $allItems = $fs->items;
    $preview  = $allItems->take($PREVIEW_LIMIT);
    $extra    = $allItems->slice($PREVIEW_LIMIT);
    $extraCnt = $extra->count();
@endphp

<div class="fps-card {{ $isSel ? 'fps-selected' : '' }}"
     onclick="fpToggle(this)"
     data-fee-id="{{ $fs->id }}">

    {{-- Toggle --}}
    <div class="fps-toggle" onclick="event.stopPropagation()">
        <div class="form-check form-switch mb-0">
            <input class="form-check-input fps-cb"
                   type="checkbox"
                   name="fee_structure_ids[]"
                   value="{{ $fs->id }}"
                   id="fee_{{ $fs->id }}"
                   {{ $isSel ? 'checked' : '' }}
                   onchange="fpSync(this)">
        </div>
    </div>

    {{-- Header --}}
    <div class="fps-hd" style="background:{{ $theme['bg'] }}">
        <div class="fps-hd-orb"></div>
        <!-- <div class="fps-icon">
            <i class="feather-layers" style="color:#fff;font-size:14px;"></i>
        </div> -->
        <p class="fps-title">{{ $fs->name }}</p>
        <span class="fps-subtitle">{{ $fs->year_label ?? 'Fee Structure' }}</span>
        <div class="fps-meta">
            <div>
                <div class="fps-total-lbl">Total</div>
                <div class="fps-total">₹{{ number_format($fs->total_amount, 0) }}</div>
            </div>
            <span class="fps-items-pill">{{ $allItems->count() }} item{{ $allItems->count() != 1 ? 's' : '' }}</span>
        </div>
    </div>

    {{-- Body --}}
    <div class="fps-body">
        {{-- Preview rows --}}
        @forelse($preview as $item)
        <div class="fps-row">
            <div class="fps-dot" style="background:{{ $theme['dot'] }}"></div>
            <span class="fps-row-name">{{ $item->feeCategory->name ?? 'Item' }}</span>
            <span class="fps-row-amt">₹{{ number_format($item->amount, 0) }}</span>
        </div>
        @empty
        <p style="text-align:center;font-size:11px;color:#9ca3af;padding:10px 0;">No breakdown</p>
        @endforelse

        {{-- Hidden extra rows --}}
        @if($extraCnt > 0)
        <div class="fps-hidden-rows" id="extra-{{ $fs->id }}">
            @foreach($extra as $item)
            <div class="fps-row">
                <div class="fps-dot" style="background:{{ $theme['dot'] }}"></div>
                <span class="fps-row-name">{{ $item->feeCategory->name ?? 'Item' }}</span>
                <span class="fps-row-amt">₹{{ number_format($item->amount, 0) }}</span>
            </div>
            @endforeach
        </div>

        {{-- +N more button --}}
        <button type="button"
                class="fps-more-btn"
                id="morebtn-{{ $fs->id }}"
                onclick="fpExpand(event, '{{ $fs->id }}', {{ $extraCnt }})">
            <i class="feather-chevron-down" style="font-size:11px;"></i>
            +{{ $extraCnt }} more row{{ $extraCnt > 1 ? 's' : '' }}
        </button>
        @endif
    </div>

    {{-- Selected stripe --}}
    <div class="fps-stripe">
        <i class="feather-check" style="font-size:9px;margin-right:3px;"></i> Selected
    </div>
</div>
@endforeach
</div>

{{-- Summary --}}
<div id="fps-summary" class="fps-summary {{ count($selectedFees) > 0 ? '' : 'fps-hidden' }}">
    <div class="fps-sum-l">
        <div class="fps-sum-ico">
            <i class="feather-check-circle" style="color:#818cf8;font-size:20px;"></i>
        </div>
        <div>
            <div class="fps-sum-lbl">Selected</div>
            <p class="fps-sum-val"><span id="fps-count">{{ count($selectedFees) }}</span> Fee Structure(s)</p>
        </div>
    </div>
    <div class="fps-sum-r">
        <div class="fps-sum-tlbl">Total Payable</div>
        <div class="fps-sum-tval" id="fps-total">
            ₹{{ number_format($feeStructures->whereIn('id', $selectedFees)->sum('total_amount'), 0) }}
        </div>
    </div>
</div>

@else
<div style="text-align:center;padding:40px 20px;background:#f9fafb;border-radius:16px;border:2px dashed #e5e7eb;">
    <div style="width:54px;height:54px;background:#f3f4f6;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px;">
        <i class="feather-file-text" style="font-size:22px;color:#9ca3af;"></i>
    </div>
    <h6 style="color:#374151;margin-bottom:6px;">No Fee Structures Found</h6>
    <p style="color:#9ca3af;font-size:13px;margin-bottom:16px;">Create fee structures for this course, branch and year first.</p>
    <a href="{{ route('admin.fee_structures.create') }}" class="btn btn-sm btn-primary px-4">
        <i class="feather-plus me-1"></i>Create Fee Structure
    </a>
</div>
@endif

<script>
const _fpsData = @json($feeStructures->keyBy('id')->map(fn($fs) => (float) $fs->total_amount));

function fpToggle(card) {
    const cb = card.querySelector('.fps-cb');
    cb.checked = !cb.checked;
    _fpApply(card, cb.checked);
    _fpSummary();
}
function fpSync(cb) {
    _fpApply(cb.closest('.fps-card'), cb.checked);
    _fpSummary();
}
function _fpApply(card, sel) {
    sel ? card.classList.add('fps-selected') : card.classList.remove('fps-selected');
}
function fpExpand(e, id, cnt) {
    e.stopPropagation();
    const rows = document.getElementById('extra-' + id);
    const btn  = document.getElementById('morebtn-' + id);
    const open = rows.classList.toggle('fps-open');
    const icon = btn.querySelector('i');
    if (open) {
        btn.classList.add('fps-expanded');
        icon.className = 'feather-chevron-up';
        btn.innerHTML = '<i class="feather-chevron-up" style="font-size:11px;"></i> Show less';
    } else {
        btn.classList.remove('fps-expanded');
        btn.innerHTML = '<i class="feather-chevron-down" style="font-size:11px;"></i> +' + cnt + ' more row' + (cnt > 1 ? 's' : '');
    }
}
function _fpSummary() {
    let total = 0, count = 0;
    document.querySelectorAll('.fps-cb:checked').forEach(cb => {
        const v = _fpsData[cb.value];
        if (v) { total += v; count++; }
    });
    document.getElementById('fps-count').textContent = count;
    document.getElementById('fps-total').textContent = '₹' + Math.round(total).toLocaleString('en-IN');
    const s = document.getElementById('fps-summary');
    count > 0 ? s.classList.remove('fps-hidden') : s.classList.add('fps-hidden');
}
document.addEventListener('DOMContentLoaded', _fpSummary);
</script>
@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
@include('elements.flash')
            {{-- ── TOP BAR ── --}}
            <div class="review-topbar sticky-top">
                <div class="rtb-left">
                    <span class="rtb-title">Review & Confirm Student Details</span>
                    <span class="rtb-step-badge">Step 3 of 3</span>
                </div>
                <div class="rtb-right">
                    <span class="rtb-progress" id="summaryText"></span>

                    <a href="javascript:history.back()" class="btn-rtb-back">
                        <i class="feather-arrow-left"></i> Back
                    </a>

                    <form action="{{ $submitRoute }}" method="POST" enctype="multipart/form-data" class="d-inline">
                        @csrf
                        @include('Academics.student._preview_hidden_inputs')
                        <button type="submit" class="btn-rtb-confirm">
                            <i class="feather-check"></i> Confirm & Save
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── BODY ── --}}
            <div class="review-body">
                @include('elements.flash')

                {{-- ── IDENTITY CARD ── --}}
                <div class="identity-card">
                    @if($student->photo_url ?? false)
                        <img src="{{ asset('storage/' . $student->photo_url) }}" class="id-avatar id-avatar--photo">
                    @elseif(!empty($data['_temp_photo_path']))
                        <img src="{{ asset('storage/' . $data['_temp_photo_path']) }}" class="id-avatar id-avatar--photo">
                    @else
                        <div class="id-avatar id-avatar--initials">
                            {{ strtoupper(substr($data['first_name'] ?? 'S',0,1)) }}{{ strtoupper(substr($data['last_name'] ?? '',0,1)) }}
                        </div>
                    @endif

                    <div class="id-info">
                        <div class="id-name">
                            {{ trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')) ?: 'Student Name Not Provided' }}
                        </div>
                        <div class="id-pills">
                            @if(!empty($data['student_status']))
                                <span class="id-pill id-pill--status">{{ ucfirst(str_replace('_',' ',$data['student_status'])) }}</span>
                            @endif
                            @foreach(['course_id_label','stream_id_label','class_id_label'] as $lbl)
                                @if(!empty($data[$lbl]))
                                    <span class="id-pill">{{ $data[$lbl] }}</span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="id-dots" id="pillsNav"></div>
                </div>

                {{-- ── MISSING FIELDS ALERT ── --}}
                @php
                    $missing = collect($sections)->flatMap(fn($s) => $s['missing_required'])->values();
                @endphp
                @if($missing->count())
                    <div class="review-alert">
                        <i class="feather-alert-circle"></i>
                        <div>
                            <strong>Required fields missing:</strong><br>
                            {{ $missing->implode(', ') }}
                        </div>
                    </div>
                @endif

                {{-- ── SECTIONS ── --}}
                <div class="sections-grid">
                    @foreach($sections as $key => $section)
                        <div class="section-card section-card--{{ $section['status'] }}" id="sec-{{ $key }}">
                            <div class="sec-header">
                                <i class="feather-{{ $section['icon'] }}"></i>
                                <span class="sec-label">{{ $section['label'] }}</span>
                                <span class="sec-badge sec-badge--{{ $section['status'] }}">
                                    @if($section['status'] === 'complete')
                                        ✔ Complete
                                    @elseif($section['status'] === 'partial')
                                        {{ $section['filled'] }}/{{ $section['total'] }} Filled
                                    @else
                                        Not Filled
                                    @endif
                                </span>
                            </div>

                            <div class="fields-grid">
                                @foreach($section['fields'] as $field)
                                    <div class="field-item {{ in_array($field['key'], ['medical_conditions','allergies']) ? 'field-item--full' : '' }}">
                                        <div class="field-label">
                                            {{ $field['label'] }} @if($field['required'])<span class="field-required">*</span>@endif
                                        </div>
                                        <div class="{{ $field['value'] ? 'field-value' : 'field-empty' }}">
                                            {{ $field['value'] ?: 'Not Provided' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ── FOOTER ── --}}
                <!-- <div class="review-footer">
                    <a href="javascript:history.back()" class="btn-footer-back">
                        <i class="feather-arrow-left"></i> Edit
                    </a>

                    <form action="{{ $submitRoute }}" method="POST">
                        @csrf
                        @include('Academics.student._preview_hidden_inputs')
                        <button type="submit" class="btn-footer-confirm">
                            <i class="feather-check"></i> Confirm & Save Student
                        </button>
                    </form>
                </div> -->
            </div>
        </div>
    </div>
</div>

<style>
/* ── Topbar ── */
.review-topbar{position:sticky;top:0;z-index:100;height:58px;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;padding:0 28px;gap:12px}
.rtb-left{display:flex;align-items:center;gap:10px}
.rtb-right{display:flex;align-items:center;gap:8px}
.rtb-title{font-size:14px;font-weight:600;color:#0f172a}
.rtb-step-badge{background:#eff6ff;color:#2563eb;font-size:11px;font-weight:500;padding:3px 10px;border-radius:20px;border:1px solid #bfdbfe}
.rtb-progress{font-size:12px;color:#64748b}

/* ── Buttons ── */
.btn-rtb-confirm,.btn-footer-confirm{display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:#fff;border:none;border-radius:8px;padding:0 18px;height:36px;font-size:13px;font-weight:500;cursor:pointer;transition:background .15s}
.btn-rtb-confirm:hover,.btn-footer-confirm:hover{background:#1d4ed8}
.btn-rtb-back,.btn-footer-back{display:inline-flex;align-items:center;gap:6px;border:1px solid #e2e8f0;background:#fff;padding:0 16px;height:36px;border-radius:8px;color:#475569;font-size:13px;text-decoration:none;transition:background .15s}
.btn-rtb-back:hover,.btn-footer-back:hover{background:#f8fafc}

/* ── Body ── */
.review-body{max-width:1080px;margin:0 auto;padding:24px 24px 48px}

/* ── Identity Card ── */
.identity-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;display:flex;align-items:center;gap:16px;padding:18px 20px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.04)}
.id-avatar{width:52px;height:52px;border-radius:50%;flex-shrink:0;object-fit:cover}
.id-avatar--initials{background:#eff6ff;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:700;border:2px solid #bfdbfe}
.id-name{font-size:15px;font-weight:700;color:#0f172a;margin-bottom:7px}
.id-pills{display:flex;flex-wrap:wrap;gap:5px}
.id-pill{background:#f8fafc;border:1px solid #e2e8f0;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:500;color:#475569}
.id-pill--status{background:#f0fdf4;color:#166534;border-color:#bbf7d0}
.id-dots{display:flex;gap:5px;margin-left:auto;align-items:center;flex-shrink:0}
.id-dot{width:9px;height:9px;border-radius:50%;cursor:pointer;transition:transform .15s}
.id-dot:hover{transform:scale(1.4)}
.id-dot.complete{background:#2563eb}
.id-dot.partial{background:#94a3b8}
.id-dot.empty{background:#e2e8f0;border:1px solid #cbd5e1}

/* ── Alert ── */
.review-alert{display:flex;gap:10px;align-items:flex-start;background:#fff7ed;border:1px solid #fed7aa;border-left:3px solid #f97316;border-radius:10px;padding:11px 14px;margin-bottom:16px;font-size:13px;color:#7c2d12}
.review-alert i{margin-top:1px;flex-shrink:0;color:#f97316}

/* ── Sections Grid ── */
.sections-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px}

.section-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04);transition:box-shadow .2s,transform .2s}
.section-card:hover{box-shadow:0 6px 20px rgba(0,0,0,.08);transform:translateY(-2px)}
.section-card--complete{border-top:3px solid #2563eb}
.section-card--partial {border-top:3px solid #94a3b8}
.section-card--empty   {border-top:3px solid #f97316}

/* Card Header */
.sec-header{padding:12px 16px;display:flex;align-items:center;gap:9px;background:#f8fafc;border-bottom:1px solid #f1f5f9}
.sec-header i{font-size:14px;color:#2563eb;flex-shrink:0}
.section-card--partial .sec-header i{color:#94a3b8}
.section-card--empty   .sec-header i{color:#f97316}
.sec-label{flex:1;font-size:13px;font-weight:600;color:#0f172a}
.sec-badge{font-size:10px;font-weight:600;padding:3px 9px;border-radius:20px}
.sec-badge--complete{background:#eff6ff;color:#2563eb}
.sec-badge--partial{background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0}
.sec-badge--empty{background:#fff7ed;color:#c2410c}

/* Fields */
.fields-grid{display:grid;grid-template-columns:1fr 1fr}
.field-item{padding:9px 16px;border-bottom:1px solid #f8fafc}
.field-item:last-child{border-bottom:none}
.field-item--full{grid-column:1/-1}
.field-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:3px}
.field-required{color:#ef4444;margin-left:1px}
.field-value{font-size:13px;font-weight:500;color:#0f172a}
.field-empty{font-size:12px;color:#cbd5e1;font-style:italic}

/* Footer */
.review-footer{margin-top:24px;padding-top:20px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:8px}
</style>

<script>
document.addEventListener('DOMContentLoaded',()=>{
    const sections=@json($sections)
    const wrap=document.getElementById('pillsNav')
    let filled=0,total=0

    Object.entries(sections).forEach(([k,s])=>{
        filled+=+s.filled; total+=+s.total
        const d=document.createElement('div')
        d.className='id-dot '+s.status
        d.title=s.label
        d.onclick=()=>document.getElementById('sec-'+k)?.scrollIntoView({behavior:'smooth'})
        wrap.appendChild(d)
    })

    document.getElementById('summaryText').textContent =
        `${filled} of ${total} fields completed (${Math.round(filled/total*100)}%)`
})
</script>
@endsection
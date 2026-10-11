@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Assign Scholarship</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('scholarships.list') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <form action="{{ route('scholarships.assign') }}" method="POST" id="assignScholarshipForm">
                        @csrf

                        {{-- Step 1: Student Search --}}
                        <div class="card stretch stretch-full mb-4">
                            <div class="card-body general-info">
                                <div class="mb-4 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-1">
                                            <i class="feather-search me-2 text-primary"></i>Step 1: Find Student
                                        </span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Enter the admission number to load student details.
                                        </span>
                                    </h5>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="admission_number" class="fw-semibold">Admission Number:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="text"
                                                class="form-control @error('admission_number') is-invalid @enderror"
                                                id="admission_number" name="admission_number"
                                                placeholder="e.g. ADM-2024-001" value="{{ old('admission_number') }}"
                                                autocomplete="off">
                                            <button type="button" class="btn btn-outline-primary" id="fetchStudentBtn">
                                                <span id="fetchBtnText"><i class="feather-search me-1"></i> Search</span>
                                                <span id="fetchBtnSpinner" class="d-none">
                                                    <span class="spinner-border spinner-border-sm me-1"></span> Searching...
                                                </span>
                                            </button>
                                        </div>
                                        @error('admission_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Student Info Panel (hidden until fetched) --}}
                                <div id="studentInfoPanel" class="d-none">
                                    <div class="row">
                                        <div class="col-12">
                                            <div
                                                class="alert alert-soft-success d-flex align-items-center gap-3 border-0 rounded-3 px-4 py-3">
                                                <div class="avatar-text avatar-lg bg-soft-success text-success fs-4 fw-bold flex-shrink-0"
                                                    id="studentAvatar">
                                                    S
                                                </div>
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 fw-bold fs-16" id="studentFullName">—</p>
                                                    <div class="d-flex flex-wrap gap-3 fs-12 text-muted">
                                                        <span><i class="feather-phone me-1"></i><span
                                                                id="studentPhone">—</span></span>
                                                        <span><i class="feather-mail me-1"></i><span
                                                                id="studentEmail">—</span></span>
                                                        <span><i class="feather-book me-1"></i>Course: <span
                                                                id="studentCourse">—</span></span>
                                                    </div>
                                                </div>
                                                <div id="assignedScholarshipsPanel" class="d-none mt-3">
    <h6 class="mb-2">Assigned Scholarships</h6>
    <ul class="list-group" id="assignedScholarshipsList"></ul>
</div>

                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="student_id" id="student_id">
                                </div>

                                {{-- Error Panel --}}
                                <div id="studentErrorPanel" class="d-none">
                                    <div class="alert alert-soft-danger border-0 rounded-3 px-4 py-3">
                                        <i class="feather-alert-circle me-2"></i>
                                        <span id="studentErrorMsg">Student not found.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Scholarship Details --}}
                        <div class="card stretch stretch-full" id="scholarshipDetailsCard"
                            style="{{ old('student_id') ? '' : 'opacity: 0.5; pointer-events: none;' }}">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-1">
                                            <i class="feather-award me-2 text-primary"></i>Step 2: Scholarship Details
                                        </span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Select a scholarship and save it as pending. Approval will clear installments
                                            later.
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="feather-check me-2"></i>Assign Scholarship
                                    </button>
                                </div>

                                {{-- Scholarship Select --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="scholarship_id" class="fw-semibold">Scholarship:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-award"></i></div>
                                            <select class="form-control @error('scholarship_id') is-invalid @enderror"
                                                id="scholarship_id" name="scholarship_id" required>
                                                <option value="">— Select Scholarship —</option>
                                                @foreach ($scholarships as $scholarship)
                                                    <option value="{{ $scholarship->id }}"
                                                        data-type="{{ $scholarship->type }}"
                                                        data-value="{{ $scholarship->value }}"
                                                        {{ old('scholarship_id') == $scholarship->id ? 'selected' : '' }}>
                                                        {{ $scholarship->name }}
                                                        ({{ ucfirst($scholarship->type) }}:
                                                        {{ $scholarship->type === 'percentage' ? $scholarship->value . '%' : '₹' . number_format($scholarship->value, 2) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('scholarship_id')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Scholarship Preview --}}
                                <div id="scholarshipPreview" class="d-none row mb-4 align-items-center">
                                    <div class="col-lg-4"></div>
                                    <div class="col-lg-8">
                                        <div class="d-flex gap-3">
                                            <div class="p-3 rounded-3 bg-soft-info text-center flex-fill">
                                                <p class="mb-1 fs-12 text-muted">Type</p>
                                                <p class="mb-0 fw-bold" id="previewType">—</p>
                                            </div>
                                            <div class="p-3 rounded-3 bg-soft-warning text-center flex-fill">
                                                <p class="mb-1 fs-12 text-muted">Scholarship Value</p>
                                                <p class="mb-0 fw-bold" id="previewValue">—</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Approved Amount --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="approved_amount" class="fw-semibold">Approved Amount (₹):</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-indian-rupee"></i>₹</div>
                                            <input type="number" step="0.01" min="0"
                                                class="form-control @error('approved_amount') is-invalid @enderror"
                                                id="approved_amount" name="approved_amount"
                                                value="{{ old('approved_amount') }}"
                                                placeholder="Will be calculated automatically" readonly>
                                        </div>
                                        <div class="fs-12 text-muted mt-1">
                                            <i class="feather-info me-1"></i>
                                            For percentage scholarships, the approved amount is calculated from the
                                            student's outstanding balance.
                                        </div>
                                        @error('approved_amount')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Notes --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="remarks" class="fw-semibold">Notes <span
                                                class="text-muted fw-normal">(optional)</span>:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-file-text"></i></div>
                                            <textarea class="form-control" id="remarks" name="remarks" rows="2"
                                                placeholder="Any additional remarks about this assignment...">{{ old('remarks') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    (function() {
        const fetchBtn = document.getElementById('fetchStudentBtn');
        const fetchBtnText = document.getElementById('fetchBtnText');
        const fetchBtnSpinner = document.getElementById('fetchBtnSpinner');
        const admissionInput = document.getElementById('admission_number');
        const infoPanel = document.getElementById('studentInfoPanel');
        const errorPanel = document.getElementById('studentErrorPanel');
        const errorMsg = document.getElementById('studentErrorMsg');
        const detailCard = document.getElementById('scholarshipDetailsCard');
        const submitBtn = document.getElementById('submitBtn');
        const scholarshipSel = document.getElementById('scholarship_id');
        const preview = document.getElementById('scholarshipPreview');
        const approvedAmount = document.getElementById('approved_amount');

        // Auto-fill if returning from validation error with student_id
        @if (old('student_id'))
            infoPanel.classList.remove('d-none');
            detailCard.style.opacity = '1';
            detailCard.style.pointerEvents = 'auto';
            submitBtn.disabled = false;
        @endif

        fetchBtn.addEventListener('click', function() {
            const admission = admissionInput.value.trim();
            if (!admission) {
                admissionInput.focus();
                return;
            }

            // Show spinner
            fetchBtnText.classList.add('d-none');
            fetchBtnSpinner.classList.remove('d-none');
            infoPanel.classList.add('d-none');
            errorPanel.classList.add('d-none');

            fetch('{{ route('scholarships.fetchStudent') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        admission_number: admission
                    })
                })
                .then(res => res.json())
                .then(data => {
                    fetchBtnText.classList.remove('d-none');
                    fetchBtnSpinner.classList.add('d-none');

                    if (data.error) {
                        errorMsg.textContent = data.error;
                        errorPanel.classList.remove('d-none');
                        detailCard.style.opacity = '0.5';
                        detailCard.style.pointerEvents = 'none';
                        submitBtn.disabled = true;
                        return;
                    }

                    // Populate student info
                    const firstName = data.first_name || '';
                    const lastName = data.last_name || data.name || '';
                    const fullName = (firstName + ' ' + lastName).trim();

                    document.getElementById('studentFullName').textContent = fullName || 'N/A';
                    document.getElementById('studentAvatar').textContent = fullName.charAt(0)
                        .toUpperCase() || 'S';
                    document.getElementById('studentPhone').textContent = data.whatsapp_number || data.mobile ||
                        '—';
                    document.getElementById('studentEmail').textContent = data.email || '—';
                    document.getElementById('studentCourse').textContent = data.course_name || data
                        .class_name || '—';
                    document.getElementById('student_id').value = data.id;

                    infoPanel.classList.remove('d-none');
                    detailCard.style.opacity = '1';
                    detailCard.style.pointerEvents = 'auto';
                    submitBtn.disabled = false;
                    // Render assigned scholarships if any
                    if (Array.isArray(data.assigned_scholarships) && data.assigned_scholarships.length > 0) {
                        const listEl = document.getElementById('assignedScholarshipsList');
                        listEl.innerHTML = '';
                        data.assigned_scholarships.forEach(sch => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.textContent = `${sch.name} (${sch.type === 'percentage' ? sch.value + '%' : '₹' + sch.value})`;
                            listEl.appendChild(li);
                        });
                        document.getElementById('assignedScholarshipsPanel').classList.remove('d-none');
                    } else {
                        document.getElementById('assignedScholarshipsPanel').classList.add('d-none');
                    }
                })
                .catch(() => {
                    fetchBtnText.classList.remove('d-none');
                    fetchBtnSpinner.classList.add('d-none');
                    errorMsg.textContent = 'Something went wrong. Please try again.';
                    errorPanel.classList.remove('d-none');
                });
        });

        // Allow pressing Enter in admission number field
        admissionInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                fetchBtn.click();
            }
        });

        // Scholarship preview
        scholarshipSel.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (!opt.value) {
                preview.classList.add('d-none');
                approvedAmount.value = '';
                return;
            }
            const type = opt.dataset.type;
            const value = parseFloat(opt.dataset.value);
            document.getElementById('previewType').textContent = type.charAt(0).toUpperCase() + type.slice(
                1);
            document.getElementById('previewValue').textContent = type === 'percentage' ?
                value + '%' :
                '₹' + value.toLocaleString('en-IN', {
                    minimumFractionDigits: 2
                });
            preview.classList.remove('d-none');
            approvedAmount.value = '';
        });
    })();
</script>
@endsection




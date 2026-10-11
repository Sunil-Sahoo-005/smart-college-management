@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-left d-flex align-items-center gap-3">
                        <h6 class="mb-0">
                            {{ !empty($library_cards) ? 'Edit Library Card' : 'New Library Card' }}
                        </h6>
                        @if(!empty($library_cards) && !empty($library_cards->student))
                            <span class="badge bg-light text-dark" style="font-size:12px;">
                                {{ $library_cards->student->first_name }} {{ $library_cards->student->last_name }}
                            </span>
                        @endif
                    </div>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.library_cards.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="feather-arrow-left me-1"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="card stretch stretch-full">
                        <form action="{{ route('admin.library_cards.store') }}" method="POST">
                            @csrf

                            @if (!empty($library_cards))
                                <input type="hidden" name="id" value="{{ $library_cards->id }}">
                            @endif

                            <div class="card-body">

                                {{-- Row 1: Student --}}
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Student <span class="text-danger">*</span></label>
                                        <select name="student_id" id="student_id" class="form-select" required>
                                            <option value="">Select Student</option>
                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ old('student_id', $library_cards->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                    @if($student->admission_number) ({{ $student->admission_number }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('student_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Row 2: Card Number + Card Serial Number --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Card Number <span class="text-danger">*</span></label>
                                        <input type="text" name="card_number" class="form-control"
                                            value="{{ old('card_number', $library_cards->card_number ?? '') }}"
                                            placeholder="LIB-1-2-00001" required>
                                        @error('card_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <small class="text-muted">Format: LIB-{tenant_id}-{sequence} (e.g., LIB-1-2-00001)</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Card Serial Number</label>
                                        <input type="text" name="card_serial_number" class="form-control"
                                            value="{{ old('card_serial_number', $library_cards->card_serial_number ?? '') }}"
                                            placeholder="Optional serial number">
                                        @error('card_serial_number')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Row 3: Issue Date + Expiry Date --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                                        <input type="date" name="issue_date" class="form-control"
                                            value="{{ old('issue_date', $library_cards?->issue_date?->format('Y-m-d')) }}"
                                            required>
                                        @error('issue_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="date" name="expiry_date" class="form-control"
                                            value="{{ old('expiry_date', $library_cards?->expiry_date?->format('Y-m-d')) }}">
                                        @error('expiry_date')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                        <small class="text-muted">Leave empty for no expiry</small>
                                    </div>
                                </div>

                                {{-- Row 4: Status + Card Validity --}}
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select" required>
                                            <option value="active" {{ old('status', $library_cards->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="suspended" {{ old('status', $library_cards->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                            <option value="expired" {{ old('status', $library_cards->status ?? '') == 'expired' ? 'selected' : '' }}>Expired</option>
                                        </select>
                                        @error('status')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Card Validity</label>
                                        <div class="mt-2">
                                            @php
                                                $expiryDate = $library_cards->expiry_date ?? null;
                                                $isExpired = $expiryDate && \Carbon\Carbon::parse($expiryDate)->isPast();
                                            @endphp
                                            @if($expiryDate && !$isExpired)
                                                <span class="badge bg-success">Valid until {{ \Carbon\Carbon::parse($expiryDate)->format('d M Y') }}</span>
                                            @elseif($expiryDate && $isExpired)
                                                <span class="badge bg-danger">Expired on {{ \Carbon\Carbon::parse($expiryDate)->format('d M Y') }}</span>
                                            @else
                                                <span class="badge bg-info">No expiry date set</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="feather-save me-1"></i>
                                        {{ !empty($library_cards) ? 'Update Card' : 'Create Card' }}
                                    </button>
                                    <a href="{{ route('admin.library_cards.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="feather-x me-1"></i> Cancel
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const issueDateInput = document.querySelector('input[name="issue_date"]');
            const expiryDateInput = document.querySelector('input[name="expiry_date"]');

            if (issueDateInput && expiryDateInput && !expiryDateInput.value) {
                issueDateInput.addEventListener('change', function () {
                    if (this.value && !expiryDateInput.value) {
                        const issueDate = new Date(this.value);
                        const expiryDate = new Date(issueDate);
                        expiryDate.setFullYear(expiryDate.getFullYear() + 1);
                        expiryDateInput.value = expiryDate.toISOString().split('T')[0];
                    }
                });
            }
        });
    </script>
@endsection
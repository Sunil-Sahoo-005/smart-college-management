@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Academic Year Info :</span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                            <a href="{{ route('admin.academic_years.index') }}" class="btn btn-secondary">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
                @include('elements.flash')
                <div class="content-area-body">

                    <form action="{{ route('admin.academic_years.store') }}" method="POST">
                        @csrf
                        @if (!empty($academicYear->id))
                            <input type="hidden" name="id" value="{{ $academicYear->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        {{ !empty($academicYear->id) ? 'Edit Academic Year' : 'Add New Academic Year' }}
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="name" class="fw-semibold">Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text">
                                                <i class="feather-hash"></i>
                                            </div>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name"
                                                value="{{ old('name', $academicYear->name ?? '') }}"
                                                placeholder="Example: 2025-26" required autocomplete="off">
                                        </div>
                                        <small class="text-muted">
                                            Auto-generated from start year and end year. You can edit it if needed.
                                        </small>
                                        @error('name')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                @php
                                    $currentYear = date('Y');
                                @endphp

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Start Year:</label>
                                    </div>

                                    <div class="col-lg-8">
                                        <select class="form-control" id="start_year" name="start_year" required>
                                            <option value="">Select Start Year</option>
                                            @for ($year = $currentYear - 5; $year <= $currentYear + 10; $year++)
                                                <option value="{{ $year }}"
                                                    {{ old('start_year', $academicYear->start_year ?? '') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">End Year:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-control" id="end_year" name="end_year" readonly>
                                            <option value="">Select End Year</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="is_current" class="fw-semibold">Current Year:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="is_current"
                                                name="is_current" value="1"
                                                {{ old('is_current', $academicYear->is_current ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_current">
                                                Set as current academic year
                                            </label>
                                        </div>
                                        @error('is_current')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startYear = document.getElementById('start_year');
            const endYear = document.getElementById('end_year');
            const nameField = document.getElementById('name');
            const maxYear = new Date().getFullYear() + 10;

            function populateEndYears() {
                let start = parseInt(startYear.value);

                endYear.innerHTML =
                    '<option value="">Select End Year</option>';

                nameField.value = '';

                if (!start) return;

                for (let year = start + 1; year <= maxYear; year++) {

                    endYear.innerHTML +=
                        `<option value="${year}">${year}</option>`;
                }
            }

            function generateName() {

                let start = startYear.value;
                let end = endYear.value;

                if (start && end) {

                    let shortEnd = end.toString().slice(-2);

                    nameField.value =
                        `${start}-${shortEnd}`;
                }
            }
            startYear.addEventListener('change', populateEndYears);
            endYear.addEventListener('change', generateName);
            populateEndYears();
        });
    </script>
@endsection

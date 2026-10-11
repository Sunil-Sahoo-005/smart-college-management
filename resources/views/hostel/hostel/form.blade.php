@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">

        {{-- SIDEBAR --}}
        @include('hostel.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            {{-- TOP HEADER --}}
            <div class="content-area-header bg-white sticky-top border-bottom d-flex align-items-center">
                <h6 class="card-title mb-0">
                    {{ !empty($hostel) ? 'Edit: ' . $hostel->name : 'New Hostel' }}
                </h6>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3"></div>
                </div>
            </div>

            {{-- BODY --}}
            <div class="content-area-body p-4">
                @include('elements.flash')

                <div class="card stretch stretch-full shadow-sm border-0">

                    {{-- CARD HEADER (empty) --}}
                    <div class="card-header bg-white border-bottom px-4 py-3">
                        {{-- intentionally empty --}}
                    </div>

                    {{-- CARD BODY --}}
                    <div class="card-body px-4 py-4">

                        <form id="hostelForm" action="{{ route('admin.hostel.store') }}" method="POST">
                            @csrf

                            @if (!empty($hostel))
                                <input type="hidden" name="id" value="{{ $hostel->id }}">
                            @endif

                            <div class="row g-4">

                                {{-- HOSTEL NAME --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Hostel Name</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $hostel->name ?? '') }}" required>

                                    @if ($errors->first())
                                        <div class="text-danger small mt-1">
                                            {{ $errors->first() }}
                                        </div>
                                    @endif
                                </div>

                                {{-- HOSTEL TYPE --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Hostel Type</label>
                                    <select name="type" class="form-select">
                                        <option value="">Select Hostel</option>
                                        <option value="boys"
                                            {{ old('type', $hostel->type ?? '') == 'boys' ? 'selected' : '' }}>
                                            Boys
                                        </option>
                                        <option value="girls"
                                            {{ old('type', $hostel->type ?? '') == 'girls' ? 'selected' : '' }}>
                                            Girls
                                        </option>
                                    </select>
                                </div>

                                {{-- HOSTEL ADDRESS --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Hostel Address</label>
                                    <textarea name="address" class="form-control" rows="4" maxlength="250"
                                        placeholder="Street, Area, City, State, PIN Code">{{ old('address', $hostel->address ?? '') }}</textarea>
                                    <small class="text-muted">Max 250 characters</small>
                                </div>

                                 {{-- CARETAKER NAME --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-medium">Caretaker Name</label>
                                    <input type="text" name="caretaker" class="form-control"
                                        value="{{ old('caretaker', $hostel->caretaker ?? '') }}"
                                        placeholder="Name of the hostel caretaker" autocomplete="off">
                                        <label class="form-label fw-medium">Caretaker Phone</label>
                                    <input type="text" name="caretaker_phone" class="form-control"
                                        value="{{ old('caretaker_phone', $hostel->caretaker_phone ?? '') }}"
                                        placeholder="Contact number of caretaker" autocomplete="off"> 
                                </div>


                                {{-- CARETAKER CONTACT (optional) --}}
                               <div class="col-12">
                                    <label class="form-label fw-medium">Code of Conduct</label>
                                    <textarea name="code_of_conduct" class="form-control" rows="6"
                                        placeholder="Rules & regulations for students staying in this hostel (e.g., timings, dress code, discipline, etc.)">{{ old('code_of_conduct', $hostel->code_of_conduct ?? '') }}</textarea>
                                    <small class="text-muted">List the hostel rules, restrictions, and expected behavior</small>
                                </div>

                                

                            </div><br>

                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.hostel.index') }}" class="btn btn-outline-secondary btn-sm">
                                    Back
                                </a>

                                <button type="submit" form="hostelForm" class="btn btn-primary btn-sm">
                                    {{ !empty($hostel) ? 'Update' : 'Save' }}
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
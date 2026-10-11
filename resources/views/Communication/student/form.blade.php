@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                           
                        </div>
                    </div>
                </div>
                <div class="content-area-body">
                    <form action="{{ route('admin.students.store') }}" method="POST">
                        @csrf
                        @if (!empty($student->id))
                            <input type="hidden" name="id" value="{{ $student->id }}">
                        @endif

                        <div class="card stretch stretch-full">
                            <div class="card-body general-info">
                                <div class="mb-5 d-flex align-items-center justify-content-between">
                                    <h5 class="fw-bold mb-0 me-4">
                                        <span class="d-block mb-2">Student Info :</span>
                                        <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                            Basic details of the student
                                        </span>
                                    </h5>
                                    <button type="submit" class="btn btn-primary">Save</button>
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
                                                value="{{ old('admission_number', $student->admission_number ?? '') }}"
                                                placeholder="Admission Number" required>
                                        </div>
                                        @error('admission_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="roll_number" class="fw-semibold">Roll Number:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="text"
                                                class="form-control @error('roll_number') is-invalid @enderror"
                                                id="roll_number" name="roll_number"
                                                value="{{ old('roll_number', $student->roll_number ?? '') }}"
                                                placeholder="Roll Number">
                                        </div>
                                        @error('roll_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="first_name" class="fw-semibold">First Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <input type="text"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                id="first_name" name="first_name"
                                                value="{{ old('first_name', $student->first_name ?? '') }}"
                                                placeholder="First Name" required>
                                        </div>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="last_name" class="fw-semibold">Last Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <input type="text"
                                                class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                                name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}"
                                                placeholder="Last Name" required>
                                        </div>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="gender" class="fw-semibold">Gender:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <select class="form-select @error('gender') is-invalid @enderror"
                                                id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male"
                                                    {{ old('gender', $student->gender ?? '') == 'male' ? 'selected' : '' }}>
                                                    Male
                                                </option>
                                                <option value="female"
                                                    {{ old('gender', $student->gender ?? '') == 'female' ? 'selected' : '' }}>
                                                    Female
                                                </option>
                                                <option value="other"
                                                    {{ old('gender', $student->gender ?? '') == 'other' ? 'selected' : '' }}>
                                                    Other
                                                </option>
                                            </select>
                                        </div>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="dob" class="fw-semibold">Date of Birth:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-calendar"></i></div>
                                            <input type="date" class="form-control @error('dob') is-invalid @enderror"
                                                id="dob" name="dob"
                                                value="{{ old('dob', $student->dob ? $student->dob->format('Y-m-d') : '') }}">
                                        </div>
                                        @error('dob')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="blood_group" class="fw-semibold">Blood Group:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-droplet"></i></div>
                                            <input type="text"
                                                class="form-control @error('blood_group') is-invalid @enderror"
                                                id="blood_group" name="blood_group"
                                                value="{{ old('blood_group', $student->blood_group ?? '') }}"
                                                placeholder="Blood Group">
                                        </div>
                                        @error('blood_group')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="religion" class="fw-semibold">Religion:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-star"></i></div>
                                            <input type="text"
                                                class="form-control @error('religion') is-invalid @enderror"
                                                id="religion" name="religion"
                                                value="{{ old('religion', $student->religion ?? '') }}"
                                                placeholder="Religion">
                                        </div>
                                        @error('religion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="nationality" class="fw-semibold">Nationality:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-globe"></i></div>
                                            <input type="text"
                                                class="form-control @error('nationality') is-invalid @enderror"
                                                id="nationality" name="nationality"
                                                value="{{ old('nationality', $student->nationality ?? '') }}"
                                                placeholder="Nationality">
                                        </div>
                                        @error('nationality')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="aadhaar" class="fw-semibold">Aadhaar:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-hash"></i></div>
                                            <input type="text"
                                                class="form-control @error('aadhaar') is-invalid @enderror" id="aadhaar"
                                                name="aadhaar" value="{{ old('aadhaar', $student->aadhaar ?? '') }}"
                                                placeholder="Aadhaar (12 digits)">
                                        </div>
                                        @error('aadhaar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="emergency_contact_name" class="fw-semibold">Emergency Contact
                                            Name:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-user"></i></div>
                                            <input type="text"
                                                class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                                id="emergency_contact_name" name="emergency_contact_name"
                                                value="{{ old('emergency_contact_name', $student->emergency_contact_name ?? '') }}"
                                                placeholder="Emergency Contact Name">
                                        </div>
                                        @error('emergency_contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="emergency_contact_phone" class="fw-semibold">Emergency Contact
                                            Phone:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-phone"></i></div>
                                            <input type="text"
                                                class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                                                id="emergency_contact_phone" name="emergency_contact_phone"
                                                value="{{ old('emergency_contact_phone', $student->emergency_contact_phone ?? '') }}"
                                                placeholder="Emergency Contact Phone">
                                        </div>
                                        @error('emergency_contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="medical_conditions" class="fw-semibold">Medical Conditions:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <textarea class="form-control @error('medical_conditions') is-invalid @enderror" id="medical_conditions"
                                            name="medical_conditions" rows="3" placeholder="Medical Conditions">{{ old('medical_conditions', $student->medical_conditions ?? '') }}</textarea>
                                        @error('medical_conditions')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="allergies" class="fw-semibold">Allergies:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <textarea class="form-control @error('allergies') is-invalid @enderror" id="allergies" name="allergies"
                                            rows="3" placeholder="Allergies">{{ old('allergies', $student->allergies ?? '') }}</textarea>
                                        @error('allergies')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="photo_url" class="fw-semibold">Photo URL:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-image"></i></div>
                                            <input type="text"
                                                class="form-control @error('photo_url') is-invalid @enderror"
                                                id="photo_url" name="photo_url"
                                                value="{{ old('photo_url', $student->photo_url ?? '') }}"
                                                placeholder="Photo URL">
                                        </div>
                                        @error('photo_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label for="status" class="fw-semibold">Status:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <div class="input-group-text"><i class="feather-shield"></i></div>
                                            <select class="form-select @error('status') is-invalid @enderror"
                                                id="status" name="status" required>
                                                <option value="">Select Status</option>
                                                <option value="active"
                                                    {{ old('status', $student->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="transfer_out"
                                                    {{ old('status', $student->status ?? 'active') == 'transfer_out' ? 'selected' : '' }}>
                                                    Transfer Out
                                                </option>
                                                <option value="alumni"
                                                    {{ old('status', $student->status ?? 'active') == 'alumni' ? 'selected' : '' }}>
                                                    Alumni
                                                </option>
                                            </select>
                                        </div>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

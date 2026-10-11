@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar')

            <div class="content-area">

                <!-- HEADER -->
                <div class="content-area-header bg-white sticky-top">
                    <h6 class="card-title mb-0">
                        {{ empty($groupMaster) ? 'Add Group Master' : 'Edit Group Master' }}
                    </h6>
                </div>

                <!-- BODY -->
                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="card stretch stretch-full">

                        @if (empty($groupMaster))
                            {{-- CREATE FORM --}}
                            <form action="{{ route('group_master.store') }}" method="POST">
                                @csrf
                        @else
                            {{-- EDIT FORM --}}
                            <form action="{{ route('group_master.update', $groupMaster) }}" method="POST">
                                @csrf
                                @method('PUT')
                        @endif

                            <div class="card-body">

                                <div class="row">
                                    <!-- Academic Group Name -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Group Name <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control"
                                               placeholder="e.g., Group A, Group B, etc."
                                               value="{{ old('name', $groupMaster->name ?? '') }}" required autocomplete="off">
                                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Strength -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Strength <span class="text-danger">*</span></label>
                                        <input type="number" name="strength" class="form-control"
                                               placeholder="Number of students"
                                               value="{{ old('strength', $groupMaster->strength ?? '') }}" required min="0" autocomplete="off">
                                        @error('strength') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Status -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active"
                                                {{ old('status', $groupMaster->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive"
                                                {{ old('status', $groupMaster->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        {{ empty($groupMaster) ? 'Save' : 'Update' }}
                                    </button>
                                    <a href="{{ route('group_master.index') }}" class="btn btn-secondary btn-sm">
                                        Cancel
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
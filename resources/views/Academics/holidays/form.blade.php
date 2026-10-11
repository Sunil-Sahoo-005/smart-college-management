@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Academics.sidebar') <!-- ✅ CHANGED HERE -->

            <div class="content-area">
                <!-- HEADER -->
                <div class="content-area-header bg-white sticky-top">
                    <h6 class="card-title mb-0">
                        {{ !empty($holiday) ? 'Edit Holiday' : 'Add Holiday' }}
                    </h6>
                </div>
                <!-- BODY -->
                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <!-- FORM -->
                        <form action="{{ route('admin.holidays.store') }}" method="POST">
                            @csrf
                            @if (!empty($holiday))
                                <input type="hidden" name="id" value="{{ $holiday->id }}">
                            @endif
                            <div class="card-body">
                                <!-- ROW 1 -->
                                <div class="row">
                                    <!-- HOLIDAY NAME -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Holiday Name</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Enter holiday name" autocomplete="off"
                                            value="{{ old('name', $holiday->name ?? '') }}">
                                    </div>
                                    <!-- DATE -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date</label>
                                        <input type="date" name="date" class="form-control"
                                            value="{{ old('date', $holiday->date ?? '') }}" autocomplete="off">
                                    </div>
                                </div>
                                <!-- ROW 2 -->
                                <div class="row">
                                    <!-- DESCRIPTION -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" placeholder="Optional description" autocomplete="off">{{ old('description', $holiday->description ?? '') }}</textarea>
                                    </div>
                                    <!-- STATUS -->
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="active"
                                                {{ old('status', $holiday->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive"
                                                {{ old('status', $holiday->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                            <!-- <option value="cancelled"
                                                    {{ old('status', $holiday->status ?? '') == 'cancelled' ? 'selected' : '' }}>
                                                    Cancelled
                                                </option> -->
                                        </select>
                                    </div>
                                </div>
                                <!-- BUTTON -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        {{ !empty($holiday) ? 'Update' : 'Save' }}
                                    </button>
                                    <a href="{{ route('admin.holidays.index') }}" class="btn btn-secondary btn-sm">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                        <!-- FORM END -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

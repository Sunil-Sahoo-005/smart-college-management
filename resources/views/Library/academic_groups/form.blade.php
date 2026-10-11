@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area">

            <!-- HEADER -->
            <div class="content-area-header bg-white sticky-top">
                <h6 class="card-title mb-0">
                    {{ !empty($academic_groups) ? 'Edit Academic Group' : 'New Academic Group' }}
                </h6>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="content-area-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">

                    <!-- FORM -->
                    <form action="{{ route('admin.academic_groups.store') }}" method="POST">
                        @csrf

                        @if (!empty($academic_groups))
                            <input type="hidden" name="id" value="{{ $academic_groups->id }}">
                        @endif

                        <div class="card-body">

                            <!-- ROW 1 -->
                            <div class="row">

                                <!-- NAME -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Group Name</label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           placeholder="Enter group name" 
                                           value="{{ old('name', $academic_groups->name ?? '') }}" autocomplete="off">
                                </div>

                                <!-- PARENT -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Parent Group</label>
                                    <select name="parent_id" class="form-control" autocomplete="off">
                                        <option value="">Select Parent</option>

                                        @foreach($parent_groups as $group)
                                            <option value="{{ $group->id }}"
                                                {{ old('parent_id', $academic_groups->parent_id ?? '') == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            <!-- ROW 2 -->
                            <div class="row">

                                <!-- STATUS -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control" autocomplete="off">
                                        <option value="1"
                                            {{ old('status', $academic_groups->status ?? 1) == 1 ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="0"
                                            {{ old('status', $academic_groups->status ?? '') == 0 ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <!-- BUTTON -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ !empty($academic_groups) ? 'Update' : 'Save' }}
                                </button>

                                <a href="{{ route('admin.academic_groups.index') }}"
                                   class="btn btn-secondary btn-sm">
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
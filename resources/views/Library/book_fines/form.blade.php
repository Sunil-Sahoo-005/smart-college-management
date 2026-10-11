@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">

            @include('Library.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">

                <div class="content-area-header bg-white sticky-top"></div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">

                        {{-- FORM START --}}
                        <form
                            action="{{ $bookFine ? route('admin.book_fines.update', $bookFine->id) : route('admin.book_fines.store') }}"
                            method="POST">

                            @csrf
                            @if ($bookFine)
                                @method('PUT')
                            @endif

                            {{-- Card Header --}}
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    {{ $bookFine ? 'Edit Fine' : 'New Book Fine' }}
                                </h6>
                            </div>

                            {{-- Card Body --}}
                            <div class="card-body">

                                <div class="row">

                                    {{-- Student --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Student <span class="text-danger">*</span>
                                        </label>

                                        <select name="student_id" class="form-control">
                                            <option value="">Select Student</option>

                                            @foreach ($students as $student)
                                                <option value="{{ $student->id }}"
                                                    {{ old('student_id', $bookFine->student_id ?? '') == $student->id ? 'selected' : '' }}>
                                                    {{ $student->first_name }} {{ $student->last_name }} ({{ $student->libraryCard->card_number }} )
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                    {{-- Amount --}}
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Amount <span class="text-danger">*</span>
                                        </label>

                                        <input type="number" step="0.01" name="amount" class="form-control"
                                            placeholder="Enter fine amount" autocomplete="off"
                                            value="{{ old('amount', $bookFine->amount ?? '') }}" required>

                                    </div>

                                </div>

                                {{-- Reason --}}
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Reason</label>
                                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter reason" autocomplete="off">{{ old('reason', $bookFine->reason ?? '') }}</textarea>
                                    </div>
                                </div>

                                {{--  BUTTONS (BOTTOM like Book Issue) --}}
                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        {{ $bookFine ? 'Update' : 'Save' }}
                                    </button>

                                    <a href="{{ route('admin.book_fines.index') }}" class="btn btn-secondary btn-sm">
                                        Cancel
                                    </a>
                                </div>

                            </div>
                            {{-- Card Body End --}}

                        </form>
                        {{-- FORM END --}}

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

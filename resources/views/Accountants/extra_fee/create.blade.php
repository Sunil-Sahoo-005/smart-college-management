@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Add Extra Fee to Student</span>
                    </h5>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.extra_fee.store') }}">
                                @csrf

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Branch (Stream)</label>
                                        <select name="stream_id" id="stream_id" class="form-select" required autocomplete="off">
                                            <option value="">Select Branch</option>
                                            @foreach ($streams as $stream)
                                                <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Academic Year</label>
                                        <select name="academic_year_id" id="academic_year_id" class="form-select" required autocomplete="off">
                                            <option value="">Select Year</option>
                                            @foreach ($academicYears as $year)
                                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Student</label>
                                        <select name="student_id" id="student_id" class="form-select" required autocomplete="off">
                                            <option value="">Select Branch & Year first</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Fee Category</label>
                                        <select name="fee_category_id" class="form-select" required autocomplete="off">
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Amount</label>
                                        <input type="number" name="amount" class="form-control" step="0.01" required autocomplete="off">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" name="due_date" class="form-control" required autocomplete="off">
                                    </div>
                                </div>
                                    @if(auth()->user()->hasPermissionTo('Create Extra Fees'))
                                <button type="submit" class="btn btn-primary">
                                    Add Extra Fee
                                </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadStudents() {
            const streamId = $('#stream_id').val();
            const academicYearId = $('#academic_year_id').val();

            if (!streamId || !academicYearId) {
                $('#student_id').html('<option value="">Select Branch & Year first</option>');
                return;
            }

            $('#student_id').html('<option value="">Loading...</option>');

            $.ajax({
                url: "{{ route('admin.ajax.students_by_branch_year') }}",
                method: 'GET',
                data: {
                    stream_id: streamId,
                    academic_year_id: academicYearId,
                },
                success: function(response) {
                    let options = '<option value="">Select Student</option>';
                    response.students.forEach(function(student) {
                        options += `<option value="${student.id}">
                            ${student.name} (${student.roll ?? 'N/A'})
                        </option>`;
                    });
                    $('#student_id').html(options);
                },
                error: function() {
                    $('#student_id').html('<option value="">Failed to load students</option>');
                }
            });
        }

        $('#stream_id, #academic_year_id').on('change', loadStudents);
    </script>
@endsection

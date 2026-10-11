@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">
                            {{ isset($feeStructure) ? 'Edit' : 'Create' }} Fee Structure
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <a href="{{ route('admin.fee_structures.index') }}" class="btn btn-secondary">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back to List</span>
                        </a>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')
                    <form
                        action="{{ isset($feeStructure) ? route('admin.fee_structures.update', $feeStructure) : route('admin.fee_structures.store') }}"
                        method="POST" novalidate>
                        @csrf
                        @isset($feeStructure)
                            @method('PUT')
                            <input type="hidden" name="course_id" value="{{ $feeStructure->course_id }}">
                            <input type="hidden" name="stream_id" value="{{ $feeStructure->stream_id }}">
                            <input type="hidden" name="year_number" value="{{ $feeStructure->year_number }}">
                        @endisset

                        <div id="step-1" class="wizard-step">
                            <div class="card stretch stretch-full">
                                <div class="card-body general-info">
                                    <div class="mb-5 d-flex align-items-center justify-content-between">
                                        <h5 class="fw-bold mb-0 me-4">
                                            <span class="d-block mb-2">Step 1: Basic Info</span>
                                            <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                                Define which course, branch, and year this fee structure applies to.
                                            </span>
                                        </h5>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(1, 2)">
                                            Next
                                        </button>
                                    </div>

                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label for="name" class="fw-semibold">Fee Structure Name:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-tag"></i></span>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    value="{{ isset($feeStructure) ? $feeStructure->name : old('name') }}"
                                                    placeholder="e.g., CSE - Year 1 Fee" required autocomplete="off"
                                                    {{ isset($feeStructure) ? 'readonly' : '' }}>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label for="course_id" class="fw-semibold">Course:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-layers"></i></span>
                                                <select class="form-select" id="course_id" name="course_id" required autocomplete="off"
                                                    {{ isset($feeStructure) ? 'disabled' : '' }}>
                                                    <option value="">Select Course</option>
                                                    @foreach ($courses as $course)
                                                        <option value="{{ $course->id }}"
                                                            {{ (isset($feeStructure) && $feeStructure->course_id == $course->id) || old('course_id') == $course->id ? 'selected' : '' }}>
                                                            {{ $course->name }} ({{ $course->duration_years }}
                                                            year{{ $course->duration_years > 1 ? 's' : '' }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label for="stream_id" class="fw-semibold">Branch:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-book-open"></i></span>
                                                <select class="form-select" id="stream_id" name="stream_id" required
                                                    disabled>
                                                    <option value="">Select Branch</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label for="year_number" class="fw-semibold">Course Year:</label>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="feather-calendar"></i></span>
                                                <select class="form-select" id="year_number" name="year_number" required
                                                    disabled>
                                                    <option value="">Select Year</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    @isset($feeStructure)
                                        <div class="alert alert-info mb-0">
                                            Step 1 is locked while editing. You can update only the fee items.
                                        </div>
                                    @endisset
                                </div>
                            </div>
                        </div>

                        <div id="step-2" class="wizard-step" style="display:none;">
                            <div class="card stretch stretch-full">
                                <div class="card-body general-info">
                                    <div class="mb-5 d-flex align-items-center justify-content-between">
                                        <h5 class="fw-bold mb-0 me-4">
                                            <span class="d-block mb-2">Step 2: Add Fee Items</span>
                                            <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                                Add each fee category with amount and optional due month.
                                            </span>
                                        </h5>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-secondary" onclick="prevStep(2, 1)">
                                                Previous
                                            </button>
                                            <button type="button" class="btn btn-primary" onclick="nextStep(2, 3)">
                                                Next
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive mb-3">
                                        <table class="table table-hover mb-0" id="feeItemsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fee Category</th>
                                                    <th>Amount</th>
                                                    <th>Due Month</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $oldItems = old('items', []);
                                                @endphp

                                                @if (!empty($oldItems))
                                                    @foreach ($oldItems as $index => $item)
                                                        <tr>
                                                            <td>
                                                                <select name="items[{{ $index }}][fee_category_id]"
                                                                    class="form-select" required>
                                                                    <option value="">Select Category</option>
                                                                    @foreach ($feeCategories as $category)
                                                                        <option value="{{ $category->id }}"
                                                                            {{ ($item['fee_category_id'] ?? null) == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="items[{{ $index }}][amount]"
                                                                    class="form-control"
                                                                    value="{{ $item['amount'] ?? '' }}" min="0.01"
                                                                    required>
                                                            </td>
                                                            <td>
                                                                <select name="items[{{ $index }}][due_month]"
                                                                    class="form-select">
                                                                    <option value="">Any Month</option>
                                                                    @for ($m = 1; $m <= 12; $m++)
                                                                        <option value="{{ $m }}"
                                                                            {{ (string) ($item['due_month'] ?? '') === (string) $m ? 'selected' : '' }}>
                                                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                                        </option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="removeRow(this)">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <script>
                                                        let rowCounter = {{ count($oldItems) }};
                                                    </script>
                                                @elseif (isset($feeStructure))
                                                    @foreach ($feeStructure->items as $index => $item)
                                                        <tr>
                                                            <td>
                                                                <select name="items[{{ $index }}][fee_category_id]"
                                                                    class="form-select" required>
                                                                    <option value="">Select Category</option>
                                                                    @foreach ($feeCategories as $category)
                                                                        <option value="{{ $category->id }}"
                                                                            {{ $item->fee_category_id == $category->id ? 'selected' : '' }}>
                                                                            {{ $category->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="items[{{ $index }}][amount]"
                                                                    class="form-control" value="{{ $item->amount }}"
                                                                    min="0.01" required>
                                                            </td>
                                                            <td>
                                                                <select name="items[{{ $index }}][due_month]"
                                                                    class="form-select">
                                                                    <option value="">Any Month</option>
                                                                    @for ($m = 1; $m <= 12; $m++)
                                                                        <option value="{{ $m }}"
                                                                            {{ $item->due_month == $m ? 'selected' : '' }}>
                                                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                                        </option>
                                                                    @endfor
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    onclick="removeRow(this)">Remove</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    <script>
                                                        let rowCounter = {{ count($feeStructure->items) }};
                                                    </script>
                                                @else
                                                    <tr>
                                                        <td>
                                                            <select name="items[0][fee_category_id]" class="form-select"
                                                                required>
                                                                <option value="">Select Category</option>
                                                                @foreach ($feeCategories as $category)
                                                                    <option value="{{ $category->id }}">
                                                                        {{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.01" name="items[0][amount]"
                                                                class="form-control" min="0.01" required>
                                                        </td>
                                                        <td>
                                                            <select name="items[0][due_month]" class="form-select">
                                                                <option value="">Any Month</option>
                                                                @for ($m = 1; $m <= 12; $m++)
                                                                    <option value="{{ $m }}">
                                                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                                                @endfor
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="removeRow(this)">Remove</button>
                                                        </td>
                                                    </tr>
                                                    <script>
                                                        let rowCounter = 1;
                                                    </script>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mb-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRow()">
                                            Add Row
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="step-3" class="wizard-step" style="display:none;">
                            <div class="card stretch stretch-full">
                                <div class="card-body general-info">
                                    <div class="mb-5 d-flex align-items-center justify-content-between">
                                        <h5 class="fw-bold mb-0 me-4">
                                            <span class="d-block mb-2">Step 3: Review & Save</span>
                                            <span class="fs-12 fw-normal text-muted text-truncate-1-line">
                                                Confirm all details before finalizing this fee structure.
                                            </span>
                                        </h5>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-secondary" onclick="prevStep(3, 2)">
                                                Previous
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                {{ isset($feeStructure) ? 'Update' : 'Save' }} Fee Structure
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="mb-2">Basic Info</h6>
                                                    <p><strong>Name:</strong> <span
                                                            id="review-name">{{ isset($feeStructure) ? $feeStructure->name : old('name') }}</span>
                                                    </p>
                                                    <p><strong>Course:</strong> <span id="review-course"></span></p>
                                                    <p><strong>Branch:</strong> <span id="review-branch"></span></p>
                                                    <p><strong>Year:</strong> <span id="review-year"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="mb-2">Total Amount</h6>
                                                    <h4 class="fw-bold" id="review-total">0.00</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fee Category</th>
                                                    <th>Amount</th>
                                                    <th>Due Month</th>
                                                </tr>
                                            </thead>
                                            <tbody id="review-items"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $courseStreamsMap = $courses->mapWithKeys(function ($course) {
            return [
                $course->id => [
                    'duration_years' => $course->duration_years,
                    'streams' => $course->streams->map(function ($stream) {
                        return [
                            'id' => $stream->id,
                            'name' => $stream->name,
                        ];
                    })->values(),
                ],
            ];
        });
    @endphp

    <script>
        const courseStreams = @json($courseStreamsMap);

        const previousValues = {
            courseId: @json(old('course_id', $feeStructure->course_id ?? null)),
            streamId: @json(old('stream_id', $feeStructure->stream_id ?? null)),
            yearNumber: @json(old('year_number', $feeStructure->year_number ?? null)),
        };

        function syncStreams() {
            const courseId = document.getElementById('course_id').value;
            const streamSelect = document.getElementById('stream_id');
            const yearSelect = document.getElementById('year_number');
            const courseData = courseStreams[courseId];

            streamSelect.innerHTML = '<option value="">Select Branch</option>';
            yearSelect.innerHTML = '<option value="">Select Year</option>';

            if (!courseData) {
                streamSelect.disabled = true;
                yearSelect.disabled = true;
                return;
            }

            courseData.streams.forEach(stream => {
                const option = document.createElement('option');
                option.value = stream.id;
                option.textContent = stream.name;
                streamSelect.appendChild(option);
            });

            streamSelect.disabled = false;

            if (previousValues.streamId && courseData.streams.some(stream => String(stream.id) === String(previousValues
                    .streamId))) {
                streamSelect.value = previousValues.streamId;
            }

            for (let year = 1; year <= Number(courseData.duration_years); year++) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = `Year ${year}`;
                yearSelect.appendChild(option);
            }

            yearSelect.disabled = false;

            if (previousValues.yearNumber && Number(previousValues.yearNumber) <= Number(courseData.duration_years)) {
                yearSelect.value = previousValues.yearNumber;
            }
        }

        function nextStep(from, to) {
            document.getElementById('step-' + from).style.display = 'none';
            document.getElementById('step-' + to).style.display = 'block';

            if (to === 3) {
                document.getElementById('review-name').textContent = document.getElementById('name').value;

                const courseSelect = document.getElementById('course_id');
                const courseOption = courseSelect.options[courseSelect.selectedIndex];
                document.getElementById('review-course').textContent = courseOption ? courseOption.text : '';

                const branchSelect = document.getElementById('stream_id');
                const branchOption = branchSelect.options[branchSelect.selectedIndex];
                document.getElementById('review-branch').textContent = branchOption ? branchOption.text : '';

                const yearSelect = document.getElementById('year_number');
                const yearOption = yearSelect.options[yearSelect.selectedIndex];
                document.getElementById('review-year').textContent = yearOption ? yearOption.text : '';

                let total = 0;
                document.querySelectorAll('#feeItemsTable tbody tr').forEach(row => {
                    const amount = parseFloat(row.querySelector('input[name*="[amount]"]').value) || 0;
                    total += amount;
                });
                document.getElementById('review-total').textContent = total.toFixed(2);

                const reviewItems = document.getElementById('review-items');
                reviewItems.innerHTML = '';
                document.querySelectorAll('#feeItemsTable tbody tr').forEach(row => {
                    const category = row.querySelector('select[name*="[fee_category_id]"] option:checked').text;
                    const amount = row.querySelector('input[name*="[amount]"]').value;
                    const dueMonth = row.querySelector('select[name*="[due_month]"] option:checked').text;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${category}</td>
                        <td>${amount}</td>
                        <td>${dueMonth}</td>
                    `;
                    reviewItems.appendChild(tr);
                });
            }
        }

        function prevStep(from, to) {
            document.getElementById('step-' + from).style.display = 'none';
            document.getElementById('step-' + to).style.display = 'block';
        }

        function addRow() {
            const tbody = document.querySelector('#feeItemsTable tbody');
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>
                    <select name="items[${rowCounter}][fee_category_id]" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach ($feeCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowCounter}][amount]" class="form-control" min="0.01" required>
                </td>
                <td>
                    <select name="items[${rowCounter}][due_month]" class="form-select">
                        <option value="">Any Month</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">Remove</button>
                </td>
            `;

            tbody.appendChild(tr);
            rowCounter++;
        }

        function removeRow(button) {
            button.closest('tr').remove();
        }

        @unless(isset($feeStructure))
            document.getElementById('course_id').addEventListener('change', function() {
                previousValues.streamId = null;
                previousValues.yearNumber = null;
                syncStreams();
            });
        @endunless

        document.addEventListener('DOMContentLoaded', function() {
            if (previousValues.courseId) {
                document.getElementById('course_id').value = previousValues.courseId;
            }

            syncStreams();
        });
    </script>
@endsection


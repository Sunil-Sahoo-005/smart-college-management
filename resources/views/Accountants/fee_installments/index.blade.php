@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Accountants.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">Fee Installments</span>
                </h5>

                <div class="page-header-right ms-auto">
                    <div class="d-flex align-items-center gap-3 page-header-right-items-wrapper">
                        @if(auth()->user()->hasPermissionTo('Create Fee Installments'))
                            <a href="{{ route('admin.fee_installments.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>
                                <span>Add Installment</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                {{-- Filter Card --}}
                <div class="card stretch stretch-full mb-4">
                    <div class="card-body">
                        <form id="filterForm" method="GET" action="{{ route('admin.fee_installments.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Student</label>
                                    <select name="student_id" class="form-select">
                                        <option value="">All Students</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->first_name }} {{ $student->last_name }}
                                                ({{ $student->admission_number ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Category</label>
                                    <select name="fee_category_id" class="form-select">
                                        <option value="">All Categories</option>
                                        @foreach($feeCategories as $category)
                                            <option value="{{ $category->id }}" {{ request('fee_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>

                                <div class="col-md-2 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="feather-search me-1"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.fee_installments.index') }}" class="btn btn-light w-100">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Ajax Content Wrapper --}}
                <div id="installmentTableWrapper">
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover" id="feeInstallmentList">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                  <tbody>
    @forelse ($installments as $inst)
        <tr class="single-item">
            <td class="project-name-td">
                <div class="hstack gap-4">
                    <div>
                        @if(auth()->user()->hasPermissionTo('Edit Fee Installments'))
                            <a href="{{ route('admin.fee_installments.edit', $inst) }}"
                               class="text-truncate-1-line">
                                {{ $inst->student?->first_name }}
                                {{ $inst->student?->last_name }}
                                ({{ $inst->student?->admission_number ?? 'N/A' }})
                            </a>
                        @else
                            <span class="text-truncate-1-line">
                                {{ $inst->student?->first_name }}
                                {{ $inst->student?->last_name }}
                                ({{ $inst->student?->admission_number ?? 'N/A' }})
                            </span>
                        @endif
                    </div>
                </div>
            </td>
            <td>{{ $inst->feeCategory?->name ?? 'N/A' }}</td>
            <td>{{ number_format($inst->amount, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}</td>
            <td>
                <span class="badge
                    @if ($inst->status == 'paid') bg-success
                    @elseif ($inst->status == 'partial') bg-warning
                    @else bg-danger @endif">
                    {{ ucfirst($inst->status) }}
                </span>
            </td>
            <td>
                <div class="hstack gap-2 justify-content-center">
                    @if(auth()->user()->hasPermissionTo('Edit Fee Installments'))
                        <a href="{{ route('admin.fee_installments.edit', $inst) }}"
                           class="avatar-text avatar-md text-warning"
                           title="Edit Installment">
                            <i class="feather-edit-3"></i>
                        </a>
                    @endif

                    @if(auth()->user()->hasPermissionTo('Delete Fee Installments'))
                        <form action="{{ route('admin.fee_installments.destroy', $inst) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this fee installment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="avatar-text avatar-md text-danger border-0 bg-transparent"
                                    title="Delete Installment">
                                <i class="feather-trash-2"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">
                No fee installments found.
                @if(auth()->user()->hasPermissionTo('Create Fee Installments'))
                    <div class="mt-2">
                        <a href="{{ route('admin.fee_installments.create') }}" class="btn btn-sm btn-primary">
                            Add your first installment
                        </a>
                    </div>
                @endif
            </td>
        </tr>
    @endforelse
</tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3" id="paginationWrapper">
                        {{ $installments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #installmentTableWrapper.loading {
        opacity: 0.6;
        pointer-events: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filterForm');
    const wrapper = document.getElementById('installmentTableWrapper');

    function loadFilteredData(url = null) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();
        const requestUrl = url ? url : `${filterForm.action}?${params}`;

        wrapper.classList.add('loading');

        fetch(requestUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newWrapper = doc.querySelector('#installmentTableWrapper');

            if (newWrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
                bindPaginationLinks();
            }

            const newUrl = url ? url : `${filterForm.action}?${params}`;
            window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
            console.error('Filter error:', error);
        })
        .finally(() => {
            wrapper.classList.remove('loading');
        });
    }

    filterForm.addEventListener('submit', function (e) {
        e.preventDefault();
        loadFilteredData();
    });

    filterForm.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', function () {
            loadFilteredData();
        });
    });

    function bindPaginationLinks() {
        document.querySelectorAll('#paginationWrapper a').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                loadFilteredData(this.href);
            });
        });
    }

    bindPaginationLinks();

    window.addEventListener('popstate', function () {
        loadFilteredData(window.location.href);
    });
});
</script>
@endsection
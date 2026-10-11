@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area">

                {{-- Header --}}
                <div
                    class="content-area-header bg-white sticky-top py-2 px-3 d-flex justify-content-between align-items-center shadow-sm">
                    <div>
                        <h5 class="fw-bold mb-0">
                            Fee Structures
                            <small class="text-muted">
                                ({{ $streams->sum(fn($s) => $s->feeStructures->count()) }})
                            </small>
                        </h5>
                    </div>

                    <div class="ms-auto">
                        @if (auth()->user()->hasPermissionTo('Create Fee Structure'))
                        <a href="{{ route('admin.fee_structures.create') }}" class="btn btn-primary">
                            <i class="feather-plus"></i> Add Fee Structure
                        </a>
                        @endif
                    </div>
                </div>

                <div class="content-area-body mt-3">
                    @include('elements.flash')

                    <div class="row g-4">

                        @foreach ($streams as $stream)
                            <div class="col-lg-6 col-xl-4">

                                <div class="card shadow-sm border-0">

                                    {{-- STREAM HEADER --}}
                                    <div class="card-header  cursor-pointer stream-toggle">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $stream->name }}</h6>
                                                <small class="text-muted">
                                                    {{ $stream->feeStructures->count() }} Structures
                                                </small>
                                            </div>
                                            <i class="feather-chevron-down toggle-icon"></i>
                                        </div>
                                    </div>

                                    {{-- STREAM BODY --}}
                                    <div class="card-body stream-body" style="display:none;">

                                        @foreach ($stream->feeStructures->groupBy(fn($fs) => $fs->course?->name ?? 'General Course') as $courseName => $structures)
                                            <div class="mb-3">

                                                <div class="fw-semibold text-primary mb-2 border-bottom pb-1">
                                                    {{ $courseName }}
                                                </div>

                                                @foreach ($structures as $fs)
                                                    <div class="p-2 mb-2 rounded border hover-box">

                                                        <div class="d-flex justify-content-between align-items-center">

                                                            <div>
                                                                <div class="fw-semibold">
                                                                    {{ $fs->name }}
                                                                </div>

                                                                <div class="small text-muted">
                                                                    {{ $fs->year_label }}
                                                                </div>

                                                                <div class="small">
                                                                    Total:
                                                                    <strong>₹{{ number_format($fs->items->sum('amount'), 0) }}</strong>
                                                                </div>
                                                            </div>

                                                           <div class="d-flex gap-1">
                                                                @if(auth()->user()->hasPermissionTo('Edit Fee Structure'))
                                                                    <a href="{{ route('admin.fee_structures.edit', $fs->id) }}"
                                                                        class="btn btn-sm btn-outline-primary">
                                                                        Edit
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>
                                        @endforeach

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        document.querySelectorAll('.stream-toggle').forEach(header => {
            header.addEventListener('click', function() {
                const body = this.nextElementSibling;
                const icon = this.querySelector('.toggle-icon');

                if (body.style.display === 'none') {
                    body.style.display = 'block';
                    icon.classList.remove('feather-chevron-down');
                    icon.classList.add('feather-chevron-up');
                } else {
                    body.style.display = 'none';
                    icon.classList.remove('feather-chevron-up');
                    icon.classList.add('feather-chevron-down');
                }
            });
        });
    </script>

    {{-- STYLE --}}
    <style>
        .cursor-pointer {
            cursor: pointer;
        }

        .hover-box {
            transition: 0.2s ease;
        }

        .hover-box:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
        }

        .card {
            border-radius: 10px;
        }

        .card-header {
            border-radius: 10px 10px 0 0;
        }

        .content-area-header {
            border-bottom: 1px solid #e0e0e0;
        }
    </style>
@endsection

@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">

            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">Questions List</span>
                </h5>
                <div class="page-header-right ms-auto d-flex gap-2">
                    <a href="{{ route('admin.question_paper_types.create', ['question_bank_id' => $questionBank->id]) }}"
                        class="btn btn-primary">
                        <i class="feather-plus me-2"></i> Add Question
                    </a>
                    <a href="{{ route('admin.question_banks.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>

            @include('elements.flash')

            <div class="content-area-body">

                {{-- Question Bank Info --}}
                <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                    <i class="feather-info me-1"></i>
                    Question Bank: <strong class="ms-1">{{ $questionBank->name }}</strong>
                    <span class="ms-3 text-muted">|</span>
                    <span class="ms-3">Total Questions: <strong>{{ $questions->count() }}</strong></span>
                </div>

                @if($questions->isEmpty())
                    <div class="text-center py-5">
                        <i class="feather-inbox" style="font-size: 48px; color: #ccc;"></i>
                        <p class="text-muted mt-3">No questions added yet.</p>
                        <a href="{{ route('admin.question_paper_types.create', ['question_bank_id' => $questionBank->id]) }}"
                            class="btn btn-primary mt-2">
                            <i class="feather-plus me-2"></i> Add First Question
                        </a>
                    </div>
                @else
                    <div class="card stretch stretch-full">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="10%">Type</th>
                                            <th width="35%">Question</th>
                                            <th width="8%">Marks</th>
                                            <th width="8%">Neg. Marks</th>
                                            <th width="15%">Explanation</th>
                                            <th width="8%">Status</th>
                                            <th width="11%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questions as $index => $q)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @php
                                                    $typeColors = [
                                                        'mcq'          => 'primary',
                                                        'true_false'   => 'info',
                                                        'short_answer' => 'warning',
                                                        'long_answer'  => 'secondary',
                                                    ];
                                                    $typeLabels = [
                                                        'mcq'          => 'MCQ',
                                                        'true_false'   => 'True/False',
                                                        'short_answer' => 'Short Ans',
                                                        'long_answer'  => 'Long Ans',
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $typeColors[$q->type] ?? 'secondary' }}">
                                                    {{ $typeLabels[$q->type] ?? $q->type }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;"
                                                    title="{{ $q->question }}">
                                                    {{ $q->question }}
                                                </div>
                                                {{-- MCQ options --}}
                                                @if($q->type === 'mcq' && $q->options->count())
                                                    <div class="mt-1">
                                                        @foreach($q->options as $opt)
                                                            <small class="{{ $opt->is_correct ? 'text-success fw-bold' : 'text-muted' }}">
                                                                <i class="feather-{{ $opt->is_correct ? 'check-circle' : 'circle' }} me-1"></i>
                                                                {{ $opt->option_text }}
                                                            </small><br>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($q->type === 'true_false' && $q->options->count())
                                                    <small class="text-muted">
                                                        Correct: <strong>{{ $q->options->firstWhere('is_correct', true)?->option_text ?? '—' }}</strong>
                                                    </small>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-success">{{ $q->mark }}</span></td>
                                            <td>
                                                <span class="badge {{ $q->negative_marks > 0 ? 'bg-danger' : 'bg-light text-dark' }}">
                                                    {{ $q->negative_marks }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-truncate text-muted fst-italic"
                                                    style="max-width: 150px;"
                                                    title="{{ $q->explanation }}">
                                                    {{ $q->explanation ?: '—' }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($q->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.question_paper_types.edit', $q->id) }}"
                                                        class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.question_paper_types.destroy', $q->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Are you sure to delete this question?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
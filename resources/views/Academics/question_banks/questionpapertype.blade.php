@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Academics.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h5 class="fw-bold mb-0 me-4">
                    <span class="d-block mb-2">
                        {{ !empty($question->id) ? 'Edit Question' : 'Add Question' }}
                    </span>
                </h5>
                <div class="page-header-right ms-auto">
                    <a href="{{ route('admin.question_banks.index') }}" class="btn btn-secondary">
                        <i class="feather-arrow-left me-2"></i> Back to List
                    </a>
                </div>
            </div>

            @include('elements.flash')

            <div class="content-area-body">

                {{-- Steps --}}
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="badge bg-success px-3 py-2">
                        <i class="feather-check me-1"></i> Step 1: Question Bank
                    </span>
                    <i class="feather-chevron-right text-muted"></i>
                    <span class="badge bg-primary px-3 py-2">
                        Step 2: Add Questions
                    </span>
                </div>

                {{-- Question Bank Info --}}
                <div class="alert alert-info d-flex align-items-center gap-2 mb-4">
                    <i class="feather-info me-1"></i>
                    Question Bank: <strong class="ms-1">{{ $questionBank->name }}</strong>
                </div>

                {{-- FORM --}}
                <form action="{{ route('admin.question_paper_types.store') }}" method="POST" id="questionForm">
                    @csrf
                    <input type="hidden" name="question_bank_id" value="{{ $questionBank->id }}">
                    @if(!empty($question->id))
                        <input type="hidden" name="id" value="{{ $question->id }}">
                    @endif
                    <input type="hidden" name="action_type" id="actionType" value="save">

                    <div class="card stretch stretch-full">
                        <div class="card-body general-info">

                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                <h5 class="fw-bold mb-0 me-4">
                                    <span class="d-block mb-2">Question Info :</span>
                                    <span class="fs-12 fw-normal text-muted">
                                        Select type and fill question details.
                                    </span>
                                </h5>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-info text-white" id="previewBtn">
                                        <i class="feather-eye me-2"></i> Preview
                                    </button>
                                    <button type="button" class="btn btn-warning text-white" id="nextBtn">
                                        <i class="feather-arrow-right me-2"></i> Next
                                    </button>
                                    <button type="button" class="btn btn-success" id="saveBtn">
                                        <i class="feather-save me-2"></i> Save
                                    </button>
                                </div>
                            </div>

                            {{-- TYPE --}}
                            <div class="row mb-4 align-items-center">
                                <div class="col-lg-4">
                                    <label class="fw-semibold">Question Type: <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <div class="input-group-text"><i class="feather-layers"></i></div>
                                        <select class="form-select @error('type') is-invalid @enderror"
                                                id="questionType" name="type" required>
                                            <option value="">-- Select Type --</option>
                                            <option value="mcq" {{ old('type', request('default_type', $question->type ?? '')) == 'mcq' ? 'selected' : '' }}>MCQ</option>
                                            <option value="true_false" {{ old('type', request('default_type', $question->type ?? '')) == 'true_false' ? 'selected' : '' }}>True / False</option>
                                            <option value="short_answer" {{ old('type', request('default_type', $question->type ?? '')) == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                            <option value="long_answer" {{ old('type', request('default_type', $question->type ?? '')) == 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                                        </select>
                                    </div>
                                    @error('type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- QUESTION FIELDS --}}
                            <div id="questionFields" style="display:none;">

                                {{-- QUESTION TEXT --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Question: <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <textarea class="form-control @error('question') is-invalid @enderror"
                                            name="question" id="questionText" rows="3"
                                            placeholder="Enter your question here...">{{ old('question', $question->question ?? '') }}</textarea>
                                        @error('question') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- MCQ OPTIONS --}}
                                <div id="mcqOptions" style="display:none;">
                                    <div class="row mb-2 align-items-center">
                                        <div class="col-lg-4">
                                            <label class="fw-semibold">Options: <span class="text-danger">*</span></label>
                                            <div class="fs-12 text-muted">Select correct option</div>
                                        </div>
                                        <div class="col-lg-8" id="optionsContainer">
                                            @for($i = 0; $i < 4; $i++)
                                            <div class="input-group mb-2 option-row">
                                                <div class="input-group-text">
                                                    <input type="radio" name="correct_option" value="{{ $i }}"
                                                        class="form-check-input mt-0" title="Mark as correct">
                                                </div>
                                                <input type="text" class="form-control"
                                                    name="options[]"
                                                    placeholder="Option {{ chr(65 + $i) }}"
                                                    value="{{ old('options.'.$i, '') }}">
                                                @if($i >= 4)
                                                <button type="button" class="btn btn-outline-danger remove-option"><i class="feather-x"></i></button>
                                                @endif
                                            </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-lg-8 offset-lg-4">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="addOption">
                                                <i class="feather-plus me-1"></i> Add Option
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- TRUE/FALSE --}}
                                <div id="trueFalseOptions" style="display:none;">
                                    <div class="row mb-4 align-items-center">
                                        <div class="col-lg-4">
                                            <label class="fw-semibold">Correct Answer: <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-lg-8">
                                            <div class="d-flex gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="true_false_answer" id="ansTrue" value="true">
                                                    <label class="form-check-label" for="ansTrue">True</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="true_false_answer" id="ansFalse" value="false">
                                                    <label class="form-check-label" for="ansFalse">False</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- MARKS --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Marks: <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <input type="number" step="0.5" min="0"
                                            class="form-control @error('mark') is-invalid @enderror"
                                            name="mark" id="markField"
                                            value="{{ old('mark', $question->mark ?? '1') }}"
                                            placeholder="e.g. 1" required>
                                        @error('mark') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- NEGATIVE MARKS --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Negative Marks:</label>
                                        <div class="fs-12 text-muted">0 = No negative marking</div>
                                    </div>
                                    <div class="col-lg-8">
                                        <input type="number" step="0.25" min="0"
                                            class="form-control @error('negative_marks') is-invalid @enderror"
                                            name="negative_marks" id="negMarkField"
                                            value="{{ old('negative_marks', $question->negative_marks ?? '0') }}"
                                            placeholder="e.g. 0.25">
                                        @error('negative_marks') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- EXPLANATION --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Explanation / Answer:</label>
                                    </div>
                                    <div class="col-lg-8">
                                        <textarea class="form-control @error('explanation') is-invalid @enderror"
                                            name="explanation" id="explanationField" rows="3"
                                            placeholder="Enter explanation (optional)">{{ old('explanation', $question->explanation ?? '') }}</textarea>
                                        @error('explanation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- STATUS --}}
                                <div class="row mb-4 align-items-center">
                                    <div class="col-lg-4">
                                        <label class="fw-semibold">Status: <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <select class="form-select @error('is_active') is-invalid @enderror"
                                                name="is_active" id="statusField" required>
                                            <option value="">Select Status</option>
                                            <option value="1" {{ old('is_active', $question->is_active ?? 1) == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('is_active', $question->is_active ?? 1) == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('is_active') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                            </div>{{-- end questionFields --}}
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{{-- PREVIEW MODAL --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="feather-eye me-2"></i> Preview Before Save
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
                    <i class="feather-alert-triangle"></i>
                    <span>Please review all details carefully before saving.</span>
                </div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th width="35%" class="bg-light">Question Bank</th>
                            <td><strong>{{ $questionBank->name }}</strong></td>
                        </tr>
                        <tr>
                            <th class="bg-light">Question Type</th>
                            <td id="prev_type" class="text-capitalize fw-semibold text-primary">—</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Question</th>
                            <td id="prev_question">—</td>
                        </tr>
                        <tr id="prev_options_row" style="display:none;">
                            <th class="bg-light">Options</th>
                            <td id="prev_options">—</td>
                        </tr>
                        <tr id="prev_tf_row" style="display:none;">
                            <th class="bg-light">Correct Answer</th>
                            <td id="prev_tf">—</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Marks</th>
                            <td id="prev_mark">—</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Negative Marks</th>
                            <td id="prev_neg_mark">—</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Explanation</th>
                            <td id="prev_explanation" class="text-muted fst-italic">—</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Status</th>
                            <td id="prev_status">—</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="feather-edit me-1"></i> Edit
                </button>
                <button type="button" class="btn btn-success" id="confirmSaveBtn">
                    <i class="feather-save me-1"></i> Confirm & Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const typeSelect       = document.getElementById('questionType');
    const questionFields   = document.getElementById('questionFields');
    const mcqOptions       = document.getElementById('mcqOptions');
    const tfOptions        = document.getElementById('trueFalseOptions');
    const optionsContainer = document.getElementById('optionsContainer');
    const addOptionBtn     = document.getElementById('addOption');
    const previewBtn       = document.getElementById('previewBtn');
    const confirmSaveBtn   = document.getElementById('confirmSaveBtn');
    const form             = document.getElementById('questionForm');
    let   optionIndex      = document.querySelectorAll('.option-row').length;

    // ── Type change ──────────────────────────────────────
    function handleTypeChange(val) {
        questionFields.style.display = val ? 'block' : 'none';
        mcqOptions.style.display     = val === 'mcq'       ? 'block' : 'none';
        tfOptions.style.display      = val === 'true_false' ? 'block' : 'none';
    }

    typeSelect.addEventListener('change', function () { handleTypeChange(this.value); });
    handleTypeChange(typeSelect.value);

    // ── Add MCQ option ───────────────────────────────────
    addOptionBtn.addEventListener('click', function () {
        const label = String.fromCharCode(65 + optionIndex);
        const div   = document.createElement('div');
        div.classList.add('input-group', 'mb-2', 'option-row');
        div.innerHTML = `
            <div class="input-group-text">
                <input type="radio" name="correct_option" value="${optionIndex}" class="form-check-input mt-0">
            </div>
            <input type="text" class="form-control" name="options[]" placeholder="Option ${label}">
            <button type="button" class="btn btn-outline-danger remove-option"><i class="feather-x"></i></button>
        `;
        optionsContainer.appendChild(div);
        optionIndex++;
    });

    // ── Remove MCQ option ────────────────────────────────
    optionsContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-option');
        if (btn) btn.closest('.option-row').remove();
    });

    // ── Type label helper ────────────────────────────────
    function typeLabel(val) {
        const map = { mcq: 'MCQ', true_false: 'True / False', short_answer: 'Short Answer', long_answer: 'Long Answer' };
        return map[val] || val;
    }

    // ── Validation helper ────────────────────────────────
    function validateForm() {
        const type     = typeSelect.value;
        const question = document.getElementById('questionText').value.trim();
        const mark     = document.getElementById('markField').value;
        if (!type)     { alert('Please select Question Type!'); return false; }
        if (!question) { alert('Please enter Question!');       return false; }
        if (!mark)     { alert('Please enter Marks!');          return false; }
        return true;
    }

    // ── Preview button ───────────────────────────────────
    previewBtn.addEventListener('click', function () {
        const type        = typeSelect.value;
        const question    = document.getElementById('questionText').value.trim();
        const mark        = document.getElementById('markField').value;
        const negMark     = document.getElementById('negMarkField').value;
        const explanation = document.getElementById('explanationField').value.trim();
        const statusEl    = document.getElementById('statusField');
        const statusText  = statusEl.options[statusEl.selectedIndex]?.text || '—';

        if (!type)     { alert('Please select Question Type!'); return; }
        if (!question) { alert('Please enter Question!');       return; }
        if (!mark)     { alert('Please enter Marks!');          return; }

        document.getElementById('prev_type').textContent        = typeLabel(type);
        document.getElementById('prev_question').textContent    = question;
        document.getElementById('prev_mark').textContent        = mark + ' marks';
        document.getElementById('prev_neg_mark').textContent    = (negMark || '0') + ' marks';
        document.getElementById('prev_explanation').textContent = explanation || 'Not provided';
        document.getElementById('prev_status').innerHTML        = statusText === 'Active'
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';

        const optRow = document.getElementById('prev_options_row');
        const tfRow  = document.getElementById('prev_tf_row');
        optRow.style.display = 'none';
        tfRow.style.display  = 'none';

        if (type === 'mcq') {
            const inputs  = document.querySelectorAll('#optionsContainer input[name="options[]"]');
            const correct = document.querySelector('#optionsContainer input[name="correct_option"]:checked');
            let html = '<ol type="A">';
            inputs.forEach((inp, idx) => {
                const isCorrect = correct && parseInt(correct.value) === idx;
                html += `<li class="${isCorrect ? 'text-success fw-bold' : ''}">${inp.value || '(empty)'} ${isCorrect ? '<span class="badge bg-success ms-1">Correct</span>' : ''}</li>`;
            });
            html += '</ol>';
            document.getElementById('prev_options').innerHTML = html;
            optRow.style.display = 'table-row';
        }

        if (type === 'true_false') {
            const tf = document.querySelector('input[name="true_false_answer"]:checked');
            document.getElementById('prev_tf').textContent = tf ? tf.value.charAt(0).toUpperCase() + tf.value.slice(1) : 'Not selected';
            tfRow.style.display = 'table-row';
        }

        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    });

    // ── Confirm Save (modal) ─────────────────────────────
    confirmSaveBtn.addEventListener('click', function () {
        document.getElementById('actionType').value = 'save';
        form.submit();
    });

    // ── NEXT button ──────────────────────────────────────
    document.getElementById('nextBtn').addEventListener('click', function () {
        if (!validateForm()) return;
        document.getElementById('actionType').value = 'next';
        form.submit();
    });

    // ── SAVE button ──────────────────────────────────────
    document.getElementById('saveBtn').addEventListener('click', function () {
        if (!validateForm()) return;
        document.getElementById('actionType').value = 'save';
        form.submit();
    });

});
</script>
@endpush
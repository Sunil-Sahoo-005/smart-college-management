{{-- ===== NOTES TAB ===== --}}
<div class="tab-pane fade" id="noteTab">
    <div class="card stretch stretch-full">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0">
                <i class="feather-file-text me-2 text-primary"></i>
                Notes — {{ $student->first_name }} {{ $student->last_name }}
            </h6>
            <button type="button" class="btn btn-primary btn-sm" id="showNoteFormBtn">
                <i class="feather-plus me-1"></i>Add Note
            </button>
        </div>
        <div class="card-body">

            {{-- ADD / EDIT FORM --}}
            <div id="noteFormWrapper" style="display:none;" class="mb-4 border rounded p-3 bg-light">
                <h6 class="fw-semibold mb-3" id="noteFormTitle">Add Note</h6>
                <form action="{{ route('admin.student.records.store', $student->id) }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="noteRecordId" value="">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea name="message" id="noteMessage" class="form-control" rows="5"
                                placeholder="Write a note..."></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Upload PDF</label>
                            <input type="file" name="file_path" class="form-control" accept=".pdf">
                            <small class="text-muted d-block mt-1">PDF only, max 10MB</small>
                            <div id="currentFileWrapper" style="display:none;" class="mt-2">
                                <span class="text-muted small">Current file:</span>
                                <a id="currentFileLink" href="#" target="_blank"
                                   class="btn btn-sm btn-outline-danger mt-1">
                                    <i class="feather-file me-1"></i>View PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="feather-save me-1"></i>
                            <span id="noteSubmitLabel">Save Note</span>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="cancelNoteBtn">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            {{-- NOTES LIST --}}
            @if ($records->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Message</th>
                                <th>File</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $index => $record)
                                <tr>
                                    <td>{{ $records->firstItem() + $index }}</td>
                                    <td>{{ $record->message ?? '—' }}</td>
                                    <td>
                                        @if ($record->file_path)
                                            <a href="{{ asset('storage/' . $record->file_path) }}"
                                               target="_blank" class="btn btn-sm btn-outline-danger">
                                                <i class="feather-file me-1"></i>View PDF
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-primary edit-note-btn"
                                                data-id="{{ $record->id }}"
                                                data-message="{{ $record->message }}"
                                                data-file="{{ $record->file_path ? asset('storage/' . $record->file_path) : '' }}">
                                                <i class="feather-edit-2 me-1"></i>Edit
                                            </button>
                                            <form action="{{ route('admin.student.records.destroy', $record->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Delete this note?')">
                                                    <i class="feather-trash-2 me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $records->links() }}</div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                        <i class="feather-inbox fs-3 text-muted"></i>
                    </div>
                    <h6 class="fw-semibold mb-1">No Notes Found</h6>
                    <p class="text-muted fs-12 mb-0">No note has been added yet.</p>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formWrapper  = document.getElementById('noteFormWrapper');
    const showBtn      = document.getElementById('showNoteFormBtn');
    const cancelBtn    = document.getElementById('cancelNoteBtn');
    const recordId     = document.getElementById('noteRecordId');
    const message      = document.getElementById('noteMessage');
    const submitLabel  = document.getElementById('noteSubmitLabel');
    const formTitle    = document.getElementById('noteFormTitle');
    const fileWrapper  = document.getElementById('currentFileWrapper');
    const fileLink     = document.getElementById('currentFileLink');

    function resetForm() {
        recordId.value = '';
        message.value  = '';
        submitLabel.textContent = 'Save Note';
        formTitle.textContent   = 'Add Note';
        fileWrapper.style.display = 'none';
    }

    showBtn.addEventListener('click', function () {
        resetForm();
        formWrapper.style.display = 'block';
        formWrapper.scrollIntoView({ behavior: 'smooth' });
    });

    cancelBtn.addEventListener('click', function () {
        formWrapper.style.display = 'none';
        resetForm();
    });

    document.querySelectorAll('.edit-note-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            recordId.value = this.dataset.id;
            message.value  = this.dataset.message;
            submitLabel.textContent = 'Update Note';
            formTitle.textContent   = 'Edit Note';

            if (this.dataset.file) {
                fileLink.href = this.dataset.file;
                fileWrapper.style.display = 'block';
            } else {
                fileWrapper.style.display = 'none';
            }

            formWrapper.style.display = 'block';
            formWrapper.scrollIntoView({ behavior: 'smooth' });
        });
    });
});
</script>
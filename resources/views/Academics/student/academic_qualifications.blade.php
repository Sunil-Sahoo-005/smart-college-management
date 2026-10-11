<div class="tab-pane fade" id="qualificationsTab">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="feather-book me-2 text-warning"></i>Academic Qualifications
                </h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addQualificationModal">
                    <i class="feather-plus me-1"></i>Add Qualification
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($academicQualifications->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3" style="width:64px;height:64px;border-radius:16px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:auto;">
                        <i class="feather-book fs-3 text-muted"></i>
                    </div>
                    <h6 class="fw-semibold mb-1">No Qualifications Added</h6>
                    <p class="text-muted fs-12 mb-0">Add 10th, 12th, Graduation etc. records here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Level</th>
                                <th>Board / University</th>
                                <th>School / College</th>
                                <th>Stream</th>
                                <th>Year</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                                <th>Roll No.</th>
                                <th>Marksheet</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($academicQualifications as $qual)
                                <tr>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst(str_replace('_', ' ', $qual->qualification_level)) }}
                                        </span>
                                    </td>
                                    <td>{{ $qual->board_university ?? '—' }}</td>
                                    <td>{{ $qual->school_college ?? '—' }}</td>
                                    <td>{{ $qual->stream ?? '—' }}</td>
                                    <td>{{ $qual->passing_year ?? '—' }}</td>
                                    <td>{{ $qual->percentage ? $qual->percentage . '%' : '—' }}</td>
                                    <td>{{ $qual->grade ?? '—' }}</td>
                                    <td>{{ $qual->roll_number ?? '—' }}</td>
                                    <td>
                                        @if($qual->certificate_path && Storage::disk('public')->exists($qual->certificate_path))
                                            <a href="{{ Storage::url($qual->certificate_path) }}" target="_blank"
                                               class="btn btn-sm btn-outline-danger py-0 px-2">
                                                <i class="feather-file-text me-1"></i>View PDF
                                            </a>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button"
                                                class="avatar-text avatar-md text-primary border-0 bg-transparent edit-qual-btn"
                                                title="Edit"
                                                data-id="{{ $qual->id }}"
                                                data-level="{{ $qual->qualification_level }}"
                                                data-board="{{ $qual->board_university }}"
                                                data-school="{{ $qual->school_college }}"
                                                data-stream="{{ $qual->stream }}"
                                                data-year="{{ $qual->passing_year }}"
                                                data-percentage="{{ $qual->percentage }}"
                                                data-grade="{{ $qual->grade }}"
                                                data-roll="{{ $qual->roll_number }}"
                                                data-has-pdf="{{ $qual->certificate_path ? '1' : '0' }}"
                                                data-url="{{ route('admin.students.qualifications.update', [$student->id, $qual->id]) }}">
                                                <i class="feather-edit-2"></i>
                                            </button>
                                            <form action="{{ route('admin.students.qualifications.destroy', [$student->id, $qual->id]) }}"
                                                  method="POST"
                                                  class="delete-qual-form"
                                                  data-qual-name="{{ ucfirst(str_replace('_', ' ', $qual->qualification_level)) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="avatar-text avatar-md text-danger border-0 bg-transparent delete-qual-btn"
                                                    title="Delete">
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
                @if($academicQualifications->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $academicQualifications->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ===== ADD MODAL ===== --}}
    <div class="modal fade" id="addQualificationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Academic Qualification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.students.qualifications.store', $student) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Qualification Level <span class="text-danger">*</span></label>
                                <select name="qualification_level" class="form-select" required>
                                    <option value="">Select Level</option>
                                    <option value="10th">10th (SSC / Matriculation)</option>
                                    <option value="12th">12th (HSC / Intermediate)</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="graduation">Graduation (UG)</option>
                                    <option value="post_graduation">Post Graduation (PG)</option>
                                    <option value="phd">PhD / Doctorate</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stream / Specialization</label>
                                <input type="text" name="stream" class="form-control" placeholder="e.g. Science, B.Tech CSE">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Board / University</label>
                                <input type="text" name="board_university" class="form-control" placeholder="e.g. CBSE, Mumbai University">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">School / College</label>
                                <input type="text" name="school_college" class="form-control" placeholder="e.g. DPS Delhi">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Passing Year</label>
                                <input type="number" name="passing_year" class="form-control" placeholder="e.g. 2020" min="1900" max="{{ date('Y') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Percentage / CGPA</label>
                                <input type="number" name="percentage" class="form-control" placeholder="e.g. 85.50" step="0.01" min="0" max="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Grade / Division</label>
                                <input type="text" name="grade" class="form-control" placeholder="e.g. A+, First Division">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Roll Number</label>
                                <input type="text" name="roll_number" class="form-control" placeholder="Exam Roll Number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Marksheet PDF</label>
                                <input type="file" name="certificate" class="form-control" accept=".pdf">
                                <small class="text-muted">Only PDF allowed (Max: 10MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== EDIT MODAL ===== --}}
    <div class="modal fade" id="editQualificationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Academic Qualification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editQualForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Qualification Level <span class="text-danger">*</span></label>
                                <select name="qualification_level" id="edit_qualification_level" class="form-select" required>
                                    <option value="10th">10th (SSC / Matriculation)</option>
                                    <option value="12th">12th (HSC / Intermediate)</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="graduation">Graduation (UG)</option>
                                    <option value="post_graduation">Post Graduation (PG)</option>
                                    <option value="phd">PhD / Doctorate</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stream / Specialization</label>
                                <input type="text" name="stream" id="edit_stream" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Board / University</label>
                                <input type="text" name="board_university" id="edit_board_university" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">School / College</label>
                                <input type="text" name="school_college" id="edit_school_college" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Passing Year</label>
                                <input type="number" name="passing_year" id="edit_passing_year" class="form-control" min="1900" max="{{ date('Y') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Percentage / CGPA</label>
                                <input type="number" name="percentage" id="edit_percentage" class="form-control" step="0.01" min="0" max="100">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Grade / Division</label>
                                <input type="text" name="grade" id="edit_grade" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Roll Number</label>
                                <input type="text" name="roll_number" id="edit_roll_number" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Replace Marksheet PDF</label>
                                <input type="file" name="certificate" id="edit_certificate" class="form-control" accept=".pdf">
                                <small id="edit_pdf_hint" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===== DELETE CONFIRM MODAL ===== --}}
    <div class="modal fade" id="deleteQualConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Delete <strong id="deleteQualName"></strong> qualification?</p>
                    <p class="text-danger small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteQualBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.edit-qual-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const d = this.dataset;
                document.getElementById('edit_qualification_level').value = d.level      || '';
                document.getElementById('edit_board_university').value    = d.board      || '';
                document.getElementById('edit_school_college').value      = d.school     || '';
                document.getElementById('edit_stream').value              = d.stream     || '';
                document.getElementById('edit_passing_year').value        = d.year       || '';
                document.getElementById('edit_percentage').value          = d.percentage || '';
                document.getElementById('edit_grade').value               = d.grade      || '';
                document.getElementById('edit_roll_number').value         = d.roll       || '';
                document.getElementById('edit_certificate').value         = '';
                document.getElementById('edit_pdf_hint').textContent = d.hasPdf === '1'
                    ? 'PDF already uploaded. Upload new to replace.'
                    : 'No PDF uploaded yet.';
                document.getElementById('editQualForm').action = d.url;
                new bootstrap.Modal(document.getElementById('editQualificationModal')).show();
            });
        });

        let qualFormToDelete = null;
        document.querySelectorAll('.delete-qual-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const form = this.closest('.delete-qual-form');
                qualFormToDelete = form;
                document.getElementById('deleteQualName').textContent = form.dataset.qualName;
                new bootstrap.Modal(document.getElementById('deleteQualConfirmModal')).show();
            });
        });
        document.getElementById('confirmDeleteQualBtn').addEventListener('click', function () {
            if (qualFormToDelete) qualFormToDelete.submit();
        });
    });
    </script>

    <style>
        .avatar-text {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 8px;
            transition: all 0.2s; cursor: pointer;
        }
        .avatar-text:hover { background-color: rgba(0,0,0,0.05); }
        .avatar-text.text-danger:hover { background-color: rgba(220,53,69,0.1); }
        .avatar-text.text-primary:hover { background-color: rgba(13,110,253,0.1); }
        .hstack { display: flex; flex-direction: row; align-items: center; }
        .gap-2 { gap: 0.5rem; }
    </style>
</div>
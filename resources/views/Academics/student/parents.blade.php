<!-- Parents Tab -->
<div class="tab-pane fade" id="parentsTab">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Parents / Guardians</h6>
                <button type="button" class="btn btn-sm btn-primary"
                        data-bs-toggle="modal" data-bs-target="#addParentModal">
                    <i class="feather-plus me-1"></i> Add Parent
                </button>
            </div>
        </div>

        <div class="card-body">
            @if($parents->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted">No parents or guardians added yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Occupation</th>
                                <th>Emergency Contact</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($parents as $parent)
                                <tr id="parent-row-{{ $parent->id }}">
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst($parent->parent_type) }}
                                        </span>
                                    </td>
                                    <td>{{ $parent->first_name }} {{ $parent->last_name }}</td>
                                    <td>{{ $parent->phone }}</td>
                                    <td>{{ $parent->email ?? 'N/A' }}</td>
                                    <td>{{ $parent->occupation ?? 'N/A' }}</td>
                                    <td>
                                        @if($parent->is_emergency_contact)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button"
                                               class="avatar-text avatar-md edit-parent-btn"
                                               title="Edit"
                                               data-id="{{ $parent->id }}"
                                               data-parent_type="{{ $parent->parent_type }}"
                                               data-first_name="{{ $parent->first_name }}"
                                               data-last_name="{{ $parent->last_name }}"
                                               data-phone="{{ $parent->phone }}"
                                               data-email="{{ $parent->email }}"
                                               data-occupation="{{ $parent->occupation }}"
                                               data-address="{{ $parent->address }}"
                                               data-relationship="{{ $parent->relationship }}"
                                               data-is_emergency_contact="{{ $parent->is_emergency_contact ? '1' : '0' }}"
                                               data-bs-toggle="modal"
                                               data-bs-target="#editParentModal">
                                                <i class="feather-edit-3"></i>
                                            </button>

                                            <form action="{{ route('admin.students.parents.destroy', [$student, $parent]) }}"
                                                  method="POST"
                                                  class="delete-parent-form"
                                                  data-parent-id="{{ $parent->id }}"
                                                  data-parent-name="{{ $parent->first_name }} {{ $parent->last_name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="avatar-text avatar-md text-danger border-0 bg-transparent delete-parent-btn"
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
                @if($parents->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $parents->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ===================== ADD PARENT MODAL ===================== --}}
    <div class="modal fade" id="addParentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Parent / Guardian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.students.parents.store', $student) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label class="form-label">Parent Type <span class="text-danger">*</span></label>
                            <select name="parent_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Local Guardian</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Relationship</label>
                                <input type="text" name="relationship" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_emergency_contact"
                                       value="1" class="form-check-input" id="add_emergency">
                                <label class="form-check-label" for="add_emergency">
                                    Emergency Contact
                                </label>
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

    {{-- ===================== EDIT PARENT MODAL ===================== --}}
    <div class="modal fade" id="editParentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Parent / Guardian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editParentForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Parent Type <span class="text-danger">*</span></label>
                            <select name="parent_type" id="edit_parent_type" class="form-select" required>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Local Guardian</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="edit_phone" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="edit_email" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Occupation</label>
                                <input type="text" name="occupation" id="edit_occupation" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Relationship</label>
                                <input type="text" name="relationship" id="edit_relationship" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" id="edit_address" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_emergency_contact"
                                       value="1" class="form-check-input" id="edit_is_emergency_contact">
                                <label class="form-check-label" for="edit_is_emergency_contact">
                                    Emergency Contact
                                </label>
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

    {{-- ===================== DELETE CONFIRMATION MODAL ===================== --}}
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteParentName"></strong>?</p>
                    <p class="text-danger small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentId = {{ $student->id }};

        document.querySelectorAll('.edit-parent-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const d = this.dataset;
                const form = document.getElementById('editParentForm');
                form.action = '/admin/students/' + studentId + '/parents/' + d.id;
                document.getElementById('edit_parent_type').value = d.parent_type || 'father';
                document.getElementById('edit_first_name').value = d.first_name || '';
                document.getElementById('edit_last_name').value = d.last_name || '';
                document.getElementById('edit_phone').value = d.phone || '';
                document.getElementById('edit_email').value = d.email || '';
                document.getElementById('edit_occupation').value = d.occupation || '';
                document.getElementById('edit_address').value = d.address || '';
                document.getElementById('edit_relationship').value = d.relationship || '';
                document.getElementById('edit_is_emergency_contact').checked = (d.is_emergency_contact === '1');
            });
        });

        let deleteFormToSubmit = null;

        document.querySelectorAll('.delete-parent-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-parent-form');
                const parentName = form.dataset.parent_name || 'this parent';
                deleteFormToSubmit = form;
                document.getElementById('deleteParentName').textContent = parentName;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                deleteModal.show();
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteFormToSubmit) {
                deleteFormToSubmit.submit();
            }
        });
    });
    </script>

    <style>
        .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); }
        .avatar-text {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 8px;
            transition: all 0.2s; cursor: pointer; border: none; background: transparent;
        }
        .avatar-text:hover { background-color: rgba(0, 0, 0, 0.05); }
        .avatar-text.text-danger:hover { background-color: rgba(220, 53, 69, 0.1); }
        .hstack { display: flex; flex-direction: row; align-items: center; }
        .gap-2 { gap: 0.5rem; }
    </style>
</div>
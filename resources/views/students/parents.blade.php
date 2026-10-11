<!-- Parents Tab -->
<div class="tab-pane fade" id="parentsTab">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Parents / Guardians</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addParentModal">
                    <i class="feather-plus me-1"></i>
                    Add Parent
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($student->parents->isEmpty())
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
                            @foreach($student->parents as $parent)
                                <tr>
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
                                            <a href="javascript:void(0);" class="avatar-text avatar-md">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="avatar-text avatar-md text-danger">
                                                <i class="feather-trash-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Parent Modal -->
    <div class="modal fade" id="addParentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Parent / Guardian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('students.parents.store', $student) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label class="form-label">Parent Type</label>
                            <select name="parent_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="father">Father</option>
                                <option value="mother">Mother</option>
                                <option value="guardian">Guardian</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Occupation</label>
                            <input type="text" name="occupation" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Relationship</label>
                            <input type="text" name="relationship" class="form-control">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_emergency_contact" value="1" class="form-check-input">
                                <label class="form-check-label">Emergency Contact</label>
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
</div>

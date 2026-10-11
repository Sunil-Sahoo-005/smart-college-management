<!-- Address Tab -->
<div class="tab-pane fade" id="addressTab">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Addresses</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="feather-plus me-1"></i>Add Address
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($addresses->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted">No addresses added yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Pincode</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($addresses as $address)
                                <tr id="address-row-{{ $address->id }}">
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst($address->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $address->address_line1 }}
                                        @if($address->address_line2), {{ $address->address_line2 }}@endif
                                    </td>
                                    <td>{{ $address->city ?? 'N/A' }}</td>
                                    <td>{{ $address->state ?? 'N/A' }}</td>
                                    <td>{{ $address->pincode ?? 'N/A' }}</td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button"
                                               class="avatar-text avatar-md edit-address-btn"
                                               title="Edit"
                                               data-id="{{ $address->id }}"
                                               data-type="{{ $address->type }}"
                                               data-address_line1="{{ $address->address_line1 }}"
                                               data-address_line2="{{ $address->address_line2 }}"
                                               data-city="{{ $address->city }}"
                                               data-state="{{ $address->state }}"
                                               data-pincode="{{ $address->pincode }}"
                                               data-country="{{ $address->country }}"
                                               data-bs-toggle="modal"
                                               data-bs-target="#editAddressModal">
                                                <i class="feather-edit-3"></i>
                                            </button>

                                            <form action="{{ route('admin.students.addresses.destroy', [$student, $address]) }}"
                                                  method="POST"
                                                  class="delete-address-form"
                                                  data-address-id="{{ $address->id }}"
                                                  data-address-type="{{ $address->type }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="avatar-text avatar-md text-danger border-0 bg-transparent delete-address-btn"
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
                @if($addresses->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $addresses->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ===================== ADD ADDRESS MODAL ===================== --}}
    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.students.addresses.store', $student) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="home">Home</option>
                                <option value="permanent">Permanent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" name="address_line1" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Line 2 (Optional)</label>
                            <input type="text" name="address_line2" class="form-control">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" value="India">
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

    {{-- ===================== EDIT ADDRESS MODAL ===================== --}}
    <div class="modal fade" id="editAddressModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAddressForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" id="edit_type" class="form-select" required>
                                <option value="home">Home</option>
                                <option value="permanent">Permanent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" name="address_line1" id="edit_address_line1" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Line 2 (Optional)</label>
                            <input type="text" name="address_line2" id="edit_address_line2" class="form-control">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" id="edit_city" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" name="state" id="edit_state" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pincode</label>
                                <input type="text" name="pincode" id="edit_pincode" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" id="edit_country" class="form-control">
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
    <div class="modal fade" id="deleteAddressConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteAddressType"></strong> address?</p>
                    <p class="text-danger small">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteAddressBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentId = {{ $student->id }};

        document.querySelectorAll('.edit-address-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const d = this.dataset;
                const form = document.getElementById('editAddressForm');
                form.action = '/admin/students/' + studentId + '/addresses/' + d.id;
                document.getElementById('edit_type').value = d.type || 'home';
                document.getElementById('edit_address_line1').value = d.address_line1 || '';
                document.getElementById('edit_address_line2').value = d.address_line2 || '';
                document.getElementById('edit_city').value = d.city || '';
                document.getElementById('edit_state').value = d.state || '';
                document.getElementById('edit_pincode').value = d.pincode || '';
                document.getElementById('edit_country').value = d.country || 'India';
            });
        });

        let deleteAddressFormToSubmit = null;

        document.querySelectorAll('.delete-address-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-address-form');
                const addressType = form.dataset.address_type || 'this';
                deleteAddressFormToSubmit = form;
                document.getElementById('deleteAddressType').textContent = addressType.toUpperCase();
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteAddressConfirmModal'));
                deleteModal.show();
            });
        });

        document.getElementById('confirmDeleteAddressBtn').addEventListener('click', function() {
            if (deleteAddressFormToSubmit) {
                deleteAddressFormToSubmit.submit();
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
<!-- Address Tab -->
<div class="tab-pane fade" id="addressTab">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Addresses</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="feather-plus me-1"></i>
                    Add Address
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($student->addresses->isEmpty())
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
                            @foreach($student->addresses as $address)
                                <tr>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst($address->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $address->address_line1 }}
                                        @if($address->address_line2)
                                            , {{ $address->address_line2 }}
                                        @endif
                                    </td>
                                    <td>{{ $address->city ?? 'N/A' }}</td>
                                    <td>{{ $address->state ?? 'N/A' }}</td>
                                    <td>{{ $address->pincode ?? 'N/A' }}</td>
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

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('students.addresses.store', $student) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="home">Home</option>
                                <option value="permanent">Permanent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address Line 1</label>
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
                                <input type="text" name="country" class="form-control">
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

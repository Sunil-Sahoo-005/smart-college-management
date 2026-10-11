@extends('admin_layout')

@section('content')
<div class="nxl-content without-header nxl-full-content">
    <div class="main-content d-flex">
        @include('Library.sidebar')

        <div class="content-area" data-scrollbar-target="#psScrollbarInit">
            <div class="content-area-header bg-white sticky-top">
                <h6 class="card-title mb-0">
                    {{ !empty($library_master) ? 'Edit Library Master' : 'New Library Master' }}
                </h6>
            </div>

            <div class="content-area-body">
                @include('elements.flash')

                <div class="card stretch stretch-full">
                    <form action="{{ route('admin.library_master.store') }}" method="POST" id="libraryForm">
                        @csrf
                        @if (!empty($library_master))
                            <input type="hidden" name="id" value="{{ $library_master->id }}">
                            <input type="hidden" name="current_type" value="{{ $library_master->type }}">
                        @endif

                        <div class="card-body">
                            <div class="row">
                                <!-- TYPE -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-control" id="type_select" required autocomplete="off">
                                        <option value="">Select Type</option>
                                        <option value="1" {{ old('type', $library_master->type ?? '') == 1 ? 'selected' : '' }}>Room No</option>
                                        <option value="2" {{ old('type', $library_master->type ?? '') == 2 ? 'selected' : '' }}>Row</option>
                                        <option value="3" {{ old('type', $library_master->type ?? '') == 3 ? 'selected' : '' }}>Shelf</option>
                                    </select>
                                    @error('type') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- PARENT (Dynamic) -->
                                <div class="col-md-6 mb-3" id="parent_field" style="display: none;">
                                    <label class="form-label">Parent <span class="text-danger">*</span></label>
                                    <select name="parent_id" class="form-control parent_select" id="parent_select" autocomplete="off">
                                        <option value="">Loading parents...</option>
                                    </select>
                                    @error('parent_id') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <!-- NAME -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        placeholder="Enter name"
                                        value="{{ old('name', $library_master->name ?? '') }}" required autocomplete="off">
                                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                </div>

                                <!-- STATUS -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-control" autocomplete="off">
                                        <option value="1" {{ old('status', $library_master->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $library_master->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ !empty($library_master) ? 'Update' : 'Save' }}
                                </button>
                                <a href="{{ route('admin.library_master.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    const typeSelect = $('#type_select');
    const parentField = $('#parent_field');
    const parentSelect = $('#parent_select');
    const isEdit = {{ !empty($library_master) ? 'true' : 'false' }};
    const currentType = $('input[name="current_type"]').val();

    // Toggle parent field based on type
    function toggleParentField(type) {
        if (type == '2' || type == '3') { // Row or Shelf
            parentField.show();
            parentSelect.prop('required', true);
            if (type) {
                loadParents(type);
            }
        } else {
            parentField.hide();
            parentSelect.prop('required', false);
            parentSelect.html('<option value="">Select Parent</option>');
        }
    }

    // Load parents via AJAX
    function loadParents(type) {
        const currentId = $('input[name="id"]').val() || 0;
        
        $.ajax({
            url: "{{ route('admin.library_master.parents') }}",
            method: 'GET',
            data: {
                type: type,
                current_id: currentId
            },
            success: function(data) {
                let options = '<option value="">Select Parent</option>';
                data.forEach(function(parent) {
                    options += `<option value="${parent.id}">${parent.name}</option>`;
                });
                parentSelect.html(options);
                
                // Restore old/selected value if exists
                const oldParent = '{{ old('parent_id', $library_master->parent_id ?? '') }}';
                if (oldParent) {
                    parentSelect.val(oldParent);
                }
            },
            error: function() {
                parentSelect.html('<option value="">Error loading parents</option>');
            }
        });
    }

    // Type change handler
    typeSelect.on('change', function() {
        toggleParentField($(this).val());
    });

    // Initial load
    if (isEdit && currentType) {
        toggleParentField(currentType);
        typeSelect.val(currentType);
        loadParents(currentType);
    } else if (typeSelect.val()) {
        toggleParentField(typeSelect.val());
    }
});
</script>

@endsection
<!-- Documents Tab -->
<div class="tab-pane fade" id="documentsTab">
    <div class="card stretch stretch-full">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">Documents</h6>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                    <i class="feather-plus me-1"></i>
                    Upload Document
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($student->documents->isEmpty())
                <div class="text-center py-4">
                    <p class="text-muted">No documents uploaded yet.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>File</th>
                                <th>Uploaded By</th>
                                <th>Uploaded At</th>
                                <th>Size</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->documents as $doc)
                                @php
                                    $disk = Storage::disk('public');
                                    $fileExists = $disk->exists($doc->file_path);
                                @endphp
                                <tr id="document-row-{{ $doc->id }}">
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst(str_replace('_', ' ', $doc->doc_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($fileExists)
                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-primary">
                                                <i class="feather-file-text me-1"></i>
                                                {{ basename($doc->file_path) }}
                                            </a>
                                        @else
                                            <span class="text-muted">
                                                <i class="feather-file-text me-1"></i>
                                                {{ basename($doc->file_path) }}
                                                <span class="badge bg-danger ms-1">Missing</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $doc->uploadedBy?->name ?? $doc->uploadedBy?->username ?? 'N/A' }}</td>
                                    <td>{{ $doc->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        @if($fileExists)
                                            @php
                                                $size = $disk->size($doc->file_path);
                                                if($size >= 1048576) {
                                                    $sizeFormatted = number_format($size / 1048576, 2) . ' MB';
                                                } else {
                                                    $sizeFormatted = number_format($size / 1024, 2) . ' KB';
                                                }
                                            @endphp
                                            {{ $sizeFormatted }}
                                        @else
                                            <span class="text-danger" title="File not found on server">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            {{-- VIEW/DOWNLOAD BUTTON --}}
                                            @if($fileExists)
                                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="avatar-text avatar-md text-info" title="View/Download">
                                                    <i class="feather-download"></i>
                                                </a>
                                            @else
                                                <span class="avatar-text avatar-md text-muted" title="File missing">
                                                    <i class="feather-download"></i>
                                                </span>
                                            @endif

                                            {{-- DELETE BUTTON (always works) --}}
                                            <form action="{{ route('admin.students.documents.destroy', [$student, $doc]) }}"
                                                  method="POST"
                                                  class="delete-document-form"
                                                  data-document-id="{{ $doc->id }}"
                                                  data-document-name="{{ basename($doc->file_path) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="avatar-text avatar-md text-danger border-0 bg-transparent delete-document-btn"
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
            @endif
        </div>
    </div>

    {{-- ===================== UPLOAD DOCUMENT MODAL ===================== --}}
    <div class="modal fade" id="addDocumentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.students.documents.store', $student) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label class="form-label">Document Type <span class="text-danger">*</span></label>
                            <select name="doc_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="admission_form">Admission Form</option>
                                <option value="marksheet">Marksheet</option>
                                <option value="transfer_certificate">Transfer Certificate</option>
                                <option value="aadhaar">Aadhaar Card</option>
                                <option value="pan_card">PAN Card</option>
                                <option value="birth_certificate">Birth Certificate</option>
                                <option value="income_certificate">Income Certificate</option>
                                <option value="caste_certificate">Caste Certificate</option>
                                <option value="photograph">Photograph</option>
                                <option value="signature">Signature</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File <span class="text-danger">*</span></label>
                            <input type="file" name="document" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Allowed: PDF, JPG, JPEG, PNG, DOC, DOCX (Max: 10MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ===================== DELETE CONFIRMATION MODAL ===================== --}}
    <div class="modal fade" id="deleteDocumentConfirmModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteDocumentName"></strong>?</p>
                    <p class="text-danger small">This action cannot be undone. The file will be permanently removed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteDocumentBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== JAVASCRIPT ===================== --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const studentId = {{ $student->id }};
        
        // ========== DELETE DOCUMENT ==========
        let deleteDocumentFormToSubmit = null;
        
        document.querySelectorAll('.delete-document-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const form = this.closest('.delete-document-form');
                const documentName = form.dataset.document_name || 'this document';
                deleteDocumentFormToSubmit = form;
                document.getElementById('deleteDocumentName').textContent = documentName;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteDocumentConfirmModal'));
                deleteModal.show();
            });
        });
        
        document.getElementById('confirmDeleteDocumentBtn').addEventListener('click', function() {
            if (deleteDocumentFormToSubmit) {
                deleteDocumentFormToSubmit.submit();
            }
        });
    });
    </script>

    <style>
        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }
        .avatar-text {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: transparent;
        }
        .avatar-text:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .avatar-text.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }
        .avatar-text.text-info:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }
        .hstack {
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        .gap-2 {
            gap: 0.5rem;
        }
    </style>
</div>
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
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($student->documents as $doc)
                                <tr>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst($doc->doc_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank">
                                            {{ basename($doc->file_path) }}
                                        </a>
                                    </td>
                                    <td>{{ $doc->uploadedBy?->Username ?? 'N/A' }}</td>
                                    <td>{{ $doc->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-end">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="avatar-text avatar-md">
                                                <i class="feather-download"></i>
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

    <!-- Upload Document Modal -->
    <div class="modal fade" id="addDocumentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('students.documents.store', $student) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="student_id" value="{{ $student->id }}">

                        <div class="mb-3">
                            <label class="form-label">Document Type</label>
                            <select name="doc_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="admission_form">Admission Form</option>
                                <option value="marksheet">Marksheet</option>
                                <option value="transfer_certificate">Transfer Certificate</option>
                                <option value="aadhaar">Aadhaar</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">File</label>
                            <input type="file" name="document" class="form-control" required>
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
</div>

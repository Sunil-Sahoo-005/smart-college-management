<!-- Manage Dues Tab -->
<div class="tab-pane fade" id="duesTab">
    <div class="card stretch stretch-full">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="card-title mb-0">Fee Installments (Dues)</h6>
        </div>
        <div class="card-body">
            @if($feeInstallments->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="feather-file-text fs-24 d-block mb-2"></i>
                    <p>No dues yet for this student.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feeInstallments as $inst)
                                <tr>
                                    <td>{{ $inst->feeCategory?->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($inst->amount, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge
                                            @if($inst->status == 'paid') bg-success
                                            @elseif($inst->status == 'partial') bg-warning
                                            @else bg-danger @endif">
                                            {{ ucfirst($inst->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($feeInstallments->hasPages())
                    <div class="d-flex justify-content-end mt-3">
                        {{ $feeInstallments->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
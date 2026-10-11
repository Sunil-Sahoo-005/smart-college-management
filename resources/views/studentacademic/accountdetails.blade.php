@extends('studentadmin_layout')
@section('content')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
        $feeProgress = $totalFees > 0 ? round(($paidFees / $totalFees) * 100) : 0;
        $totalOutstanding = $unpaidInstallments->sum('remaining_amount');
        $nextInstallment = $unpaidInstallments->sortBy('due_date')->first();
    @endphp

    <style>
        * {
            box-sizing: border-box;
        }

        body,
        .nxl-content {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top right, rgba(190, 217, 193, 0.46), transparent 28%),
                linear-gradient(180deg, #f5efe3 0%, #eef3ea 100%);
            color: #18352d;
        }

        .account-shell {
            padding: 26px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .hero-panel {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 28px;
            background:
                radial-gradient(circle at top left, rgba(255, 255, 255, 0.14), transparent 24%),
                linear-gradient(135deg, #12342b 0%, #1d5548 58%, #c68b3c 160%);
            color: #f7f3ea;
            box-shadow: 0 20px 55px rgba(19, 52, 43, 0.18);
        }

        .hero-panel::after {
            content: '';
            position: absolute;
            inset: auto -70px -90px auto;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
        }

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1.55fr) minmax(300px, 0.95fr);
            gap: 18px;
            align-items: stretch;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: #f6e7ce;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .hero-title {
            margin: 16px 0 8px;
            font-size: clamp(28px, 4vw, 40px);
            line-height: 1.06;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #fffaf3;
        }

        .hero-copy {
            max-width: 700px;
            margin: 0;
            color: rgba(247, 243, 234, 0.82);
            font-size: 14px;
            line-height: 1.7;
        }

        .hero-chips {
            margin-top: 18px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #f7f3ea;
            font-size: 13px;
            font-weight: 700;
        }

        .hero-side {
            display: grid;
            gap: 16px;
        }

        .summary-panel,
        .due-panel {
            border-radius: 22px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(10px);
        }

        .summary-grid {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .summary-item {
            border-radius: 16px;
            padding: 14px;
            background: rgba(7, 20, 16, 0.16);
        }

        .summary-label,
        .due-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(247, 243, 234, 0.68);
            font-weight: 700;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 19px;
            font-weight: 800;
            color: #fffaf3;
            line-height: 1.35;
        }

        .due-value {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin: 8px 0 6px;
            color: #fffaf3;
        }

        .due-copy {
            font-size: 13px;
            line-height: 1.6;
            color: rgba(247, 243, 234, 0.8);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }

        .stat-card,
        .panel-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(24, 53, 45, 0.08);
            box-shadow: 0 16px 45px rgba(28, 49, 43, 0.08);
            backdrop-filter: blur(14px);
        }

        .stat-card {
            border-radius: 22px;
            padding: 18px;
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7f77;
            font-weight: 700;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #edf5ef;
            color: #12342b;
            font-size: 18px;
        }

        .stat-value {
            font-size: 30px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #18352d;
            margin-bottom: 8px;
        }

        .stat-copy {
            font-size: 13px;
            line-height: 1.6;
            color: #687872;
        }

        .panel-card {
            border-radius: 26px;
            padding: 22px;
        }

        .panel-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .panel-kicker {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8a7c64;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .panel-title {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: #16342d;
        }

        .panel-copy {
            margin: 6px 0 0;
            color: #667871;
            font-size: 13px;
            line-height: 1.6;
        }

        .badge-soft {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 999px;
            background: #f5efe2;
            color: #85551a;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .profile-card,
        .alert-card,
        .installment-card,
        .note-card {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #faf7f1 0%, #edf4ee 100%);
            border: 1px solid rgba(24, 53, 45, 0.07);
        }

        .profile-label,
        .installment-label,
        .note-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .profile-value,
        .installment-value,
        .note-title {
            font-size: 15px;
            font-weight: 700;
            color: #18352d;
            line-height: 1.45;
        }

        .progress-shell {
            margin-top: 18px;
        }

        .progress-topline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 700;
            color: #506660;
            margin-bottom: 10px;
        }

        .progress-track {
            height: 12px;
            border-radius: 999px;
            overflow: hidden;
            background: #e8ecdf;
        }

        .progress-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #12342b 0%, #3f7d69 100%);
        }

        .installment-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .installment-card {
            display: grid;
            grid-template-columns: 28px minmax(0, 1.6fr) minmax(120px, 0.9fr) minmax(110px, 0.8fr) minmax(120px, 0.8fr);
            align-items: center;
            gap: 14px;
            background: #fbf8f2;
            padding: 14px 16px;
        }

        .installment-main {
            min-width: 0;
        }

        .installment-title {
            font-size: 15px;
            font-weight: 800;
            color: #18352d;
            margin-bottom: 3px;
            line-height: 1.35;
        }

        .installment-sub {
            font-size: 12px;
            color: #687872;
            line-height: 1.5;
        }

        .installment-stack {
            min-width: 0;
        }

        .stack-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #8a9a94;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .stack-value {
            font-size: 13px;
            font-weight: 700;
            color: #18352d;
            line-height: 1.4;
        }

        .installment-amount {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: #85551a;
            background: #f6e7ce;
            border-radius: 999px;
            padding: 8px 10px;
            white-space: nowrap;
        }

        .check-wrap {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .custom-check {
            width: 18px;
            height: 18px;
            accent-color: #12342b;
            cursor: pointer;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            text-transform: capitalize;
        }

        .status-pill::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.7;
        }

        .status-pill.unpaid {
            background: #fde9e5;
            color: #a04231;
        }

        .status-pill.partial {
            background: #fff3dd;
            color: #8a5f17;
        }

        .status-pill.completed {
            background: #eef5ef;
            color: #21493d;
        }

        .payment-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid rgba(24, 53, 45, 0.08);
        }

        .selected-block {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .selected-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            font-weight: 700;
        }

        .selected-total {
            font-size: 24px;
            font-weight: 800;
            color: #18352d;
            letter-spacing: -0.04em;
        }

        .btn-pay {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: 14px;
            background: #12342b;
            color: #f7f3ea;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.16s ease, background 0.16s ease, opacity 0.16s ease;
        }

        .btn-pay:hover {
            background: #1d5548;
            transform: translateY(-1px);
        }

        .btn-pay:disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none;
        }

        .summary-table-wrap {
            overflow-x: auto;
            border-radius: 18px;
            border: 1px solid rgba(24, 53, 45, 0.08);
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        .summary-table thead th {
            padding: 12px 14px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            background: #f7f0e2;
            border-bottom: 1px solid rgba(24, 53, 45, 0.08);
            text-align: left;
        }

        .summary-table tbody tr {
            border-bottom: 1px solid rgba(24, 53, 45, 0.08);
        }

        .summary-table tbody tr:last-child {
            border-bottom: none;
        }

        .summary-table tbody tr:hover {
            background: #fbf8f2;
        }

        .summary-table td {
            padding: 14px;
            font-size: 13px;
            color: #18352d;
            vertical-align: middle;
        }

        .paid-amt {
            color: #1f5d46;
            font-weight: 800;
        }

        .due-amt {
            color: #a04231;
            font-weight: 800;
        }

        .prog-wrap {
            background: #e8ecdf;
            border-radius: 999px;
            height: 10px;
            overflow: hidden;
        }

        .prog-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #c68b3c 0%, #12342b 100%);
            transition: width 0.4s;
        }

        .all-clear {
            border-radius: 22px;
            padding: 44px 24px;
            text-align: center;
            background: linear-gradient(180deg, #faf7f1 0%, #f1f5ef 100%);
            border: 1px dashed rgba(24, 53, 45, 0.12);
        }

        .all-clear-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eaf5ee;
            color: #1f5d46;
            font-size: 28px;
            margin-bottom: 14px;
        }

        .modal-content {
            border-radius: 20px;
            overflow: hidden;
            border: none;
        }

        .modal-header-custom {
            background: #12342b;
            color: #f7f3ea;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header-custom h5 {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            color: #f7f3ea;
            font-size: 20px;
            cursor: pointer;
            line-height: 1;
        }

        .modal-body {
            padding: 24px;
        }

        .pay-amount-display {
            text-align: center;
            padding: 8px 0 20px;
        }

        .pay-amount-display .big-amt {
            font-size: 36px;
            font-weight: 800;
            color: #18352d;
        }

        .form-group label {
            font-size: 11px;
            font-weight: 700;
            color: #7a8a84;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            display: block;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid rgba(24, 53, 45, 0.16);
            border-radius: 12px;
            font-size: 13.5px;
            font-weight: 600;
            color: #18352d;
            background: #fff;
        }

        .btn-confirm-pay {
            width: 100%;
            padding: 13px;
            background: #12342b;
            color: #f7f3ea;
            font-size: 14px;
            font-weight: 800;
            border: none;
            border-radius: 14px;
            cursor: pointer;
        }

        @media (max-width: 1100px) {
            .hero-grid,
            .stats-grid,
            .profile-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .account-shell {
                padding: 16px;
            }

            .hero-grid,
            .summary-grid,
            .stats-grid,
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .installment-card {
                grid-template-columns: 28px 1fr;
                align-items: flex-start;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="account-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">
                            <i class="feather-dollar-sign me-2"></i>Financial Portal
                        </span>
                        <h1 class="hero-title">Fee Management</h1>
                        <p class="hero-copy">
                            Review your enrollment fees, track payment progress, and clear outstanding installments through
                            our secure gateway. Keeping your account updated ensures uninterrupted access to academic
                            services.
                        </p>
                        <div class="hero-chips">
                            <span class="hero-chip">
                                <i class="feather-user me-2"></i>{{ $studentName }}
                            </span>
                            <span class="hero-chip">
                                <i class="feather-hash me-2"></i>Reg: {{ $student->admission_number ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="hero-side">
                        <div class="due-panel">
                            <div class="due-label">Total Outstanding</div>
                            <div class="due-value">₹{{ number_format($totalOutstanding, 0) }}</div>
                            <div class="due-copy">
                                @if ($nextInstallment)
                                    Next due: ₹{{ number_format($nextInstallment->remaining_amount, 0) }} for
                                    {{ $nextInstallment->category_name }} by
                                    {{ \Carbon\Carbon::parse($nextInstallment->due_date)->format('d M Y') }}
                                @else
                                    No immediate dues detected.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Total Fees</div>
                        <div class="stat-icon"><i class="feather-file-text"></i></div>
                    </div>
                    <div class="stat-value">₹{{ number_format($totalFees, 0) }}</div>
                    <div class="stat-copy">Aggregate program cost.</div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Paid Amount</div>
                        <div class="stat-icon" style="background:#eef5ef; color:#21493d;"><i class="feather-check-circle"></i></div>
                    </div>
                    <div class="stat-value">₹{{ number_format($paidFees, 0) }}</div>
                    <div class="stat-copy">Successfully cleared amount.</div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Remaining</div>
                        <div class="stat-icon" style="background:#fde9e5; color:#a04231;"><i class="feather-alert-circle"></i></div>
                    </div>
                    <div class="stat-value">₹{{ number_format($dueFees, 0) }}</div>
                    <div class="stat-copy">Pending balance to be paid.</div>
                </div>
                <div class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Progress</div>
                        <div class="stat-icon" style="background:#f1f4f9; color:#3e5b8a;"><i class="feather-trending-up"></i></div>
                    </div>
                    <div class="stat-value">{{ $feeProgress }}%</div>
                    <div class="stat-copy">Payment lifecycle completion.</div>
                </div>
            </section>

            <div class="row g-4">
                <div class="col-xl-8">
                    {{-- Installments --}}
                    <article class="panel-card h-100">
                        <div class="panel-head">
                            <div>
                                <div class="panel-kicker">Payment Portal</div>
                                <h2 class="panel-title">Pending Installments</h2>
                                <p class="panel-copy">Select installments to proceed with payment.</p>
                            </div>
                        </div>

                        @if ($unpaidInstallments->count() > 0)
                            <div class="installment-list">
                                @foreach ($unpaidInstallments as $installment)
                                    <div class="installment-card" data-id="{{ $installment->id }}"
                                        data-amount="{{ $installment->remaining_amount }}">
                                        <div class="check-wrap">
                                            <input type="checkbox" class="custom-check inst-check"
                                                data-id="{{ $installment->id }}"
                                                data-amount="{{ $installment->remaining_amount }}">
                                        </div>
                                        <div class="installment-main">
                                            <div class="installment-title">{{ $installment->category_name }}</div>
                                            <div class="installment-sub">Due:
                                                {{ \Carbon\Carbon::parse($installment->due_date)->format('d M Y') }}</div>
                                        </div>
                                        <div class="installment-stack">
                                            <div class="stack-label">Already Paid</div>
                                            <div class="stack-value">₹{{ number_format($installment->already_paid, 0) }}</div>
                                        </div>
                                        <div class="installment-stack text-end">
                                            <div class="stack-label">Balance</div>
                                            <div class="installment-amount">₹{{ number_format($installment->remaining_amount, 0) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="status-pill {{ $installment->already_paid > 0 ? 'partial' : 'unpaid' }}">
                                                {{ $installment->already_paid > 0 ? 'Partial' : 'Unpaid' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="payment-bar">
                                <div class="selected-block">
                                    <div class="selected-label">Selected Total</div>
                                    <div class="selected-total">₹<span id="selected-amount-display">0</span></div>
                                </div>
                                <button class="btn-pay" id="pay-now-btn" disabled>
                                    <i class="feather-credit-card"></i> Pay Selected Now
                                </button>
                            </div>
                        @else
                            <div class="all-clear">
                                <div class="all-clear-icon"><i class="feather-check"></i></div>
                                <h3 class="all-clear-title">All Caught Up!</h3>
                                <p class="all-clear-copy">You have no pending installments. All your registered fee
                                    categories are cleared.</p>
                            </div>
                        @endif
                    </article>
                </div>

                <div class="col-xl-4">
                    {{-- Summary Table --}}
                    <article class="panel-card h-100">
                        <div class="panel-head">
                            <div>
                                <div class="panel-kicker">Account Summary</div>
                                <h2 class="panel-title">Fee Categories</h2>
                                <p class="panel-copy">Total breakdown by fee type.</p>
                            </div>
                        </div>

                        <div class="summary-table-wrap">
                            <table class="summary-table">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categoryFees as $cat)
                                        @php
                                            $catProgress = $cat->total_amount > 0 ? round(($cat->paid_amount / $cat->total_amount) * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $cat->category_name }}</div>
                                                <div class="small text-muted mt-1">
                                                    Paid: <span class="paid-amt">₹{{ number_format($cat->paid_amount, 0) }}</span> /
                                                    ₹{{ number_format($cat->total_amount, 0) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="prog-wrap flex-grow-1">
                                                        <div class="prog-fill" style="width: {{ $catProgress }}%"></div>
                                                    </div>
                                                    <span class="small fw-bold">{{ $catProgress }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </article>
                </div>

                <div class="col-xl-12 mt-4">
                    {{-- Payment History --}}
                    <article class="panel-card">
                        <div class="panel-head">
                            <div>
                                <div class="panel-kicker">Transaction Records</div>
                                <h2 class="panel-title">Payment History</h2>
                                <p class="panel-copy">Your complete transaction history for this academic year.</p>
                            </div>
                        </div>

                        @if ($paymentHistory->count() > 0)
                            <div class="summary-table-wrap">
                                <table class="summary-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Amount Paid</th>
                                            <th>Method</th>
                                            <th>Transaction ID</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($paymentHistory as $payment)
                                            <tr>
                                                <td class="fw-bold">{{ \Carbon\Carbon::parse($payment->paid_at)->format('d M Y') }}</td>
                                                <td class="fw-bold">{{ $payment->receipt_number }}</td>
                                                <td class="fw-bold text-success">₹{{ number_format($payment->amount_paid, 0) }}</td>
                                                <td><span class="text-uppercase small fw-bold">{{ $payment->payment_method }}</span></td>
                                                <td class="text-muted small">{{ $payment->transaction_id ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="status-pill completed">Completed</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="all-clear">
                                <p class="all-clear-copy">No previous payments recorded.</p>
                            </div>
                        @endif
                    </article>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header-custom">
                    <h5>Confirm Payment</h5>
                    <button class="modal-close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="pay-amount-display">
                        <div class="big-amt">₹<span id="modal-amount">0</span></div>
                        <p>Total amount to be charged</p>
                    </div>

                    <div class="form-group mb-3">
                        <label>Student Name</label>
                        <input type="text" class="form-input" value="{{ $studentName }}" readonly>
                    </div>

                    <div class="form-group mb-4">
                        <label>Payment Method</label>
                        <input type="text" class="form-input" value="Secure Online Payment (Razorpay)" readonly>
                    </div>

                    <button class="btn-confirm-pay" id="initiate-razorpay">
                        Securely Pay ₹<span id="modal-amount-btn">0</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checks = document.querySelectorAll('.inst-check');
            const display = document.getElementById('selected-amount-display');
            const payBtn = document.getElementById('pay-now-btn');
            const modalAmt = document.getElementById('modal-amount');
            const modalAmtBtn = document.getElementById('modal-amount-btn');

            let selectedIds = [];
            let totalAmount = 0;

            checks.forEach(check => {
                check.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const amt = parseFloat(this.dataset.amount);

                    if (this.checked) {
                        selectedIds.push(id);
                        totalAmount += amt;
                    } else {
                        selectedIds = selectedIds.filter(i => i !== id);
                        totalAmount -= amt;
                    }

                    display.innerText = totalAmount.toLocaleString();
                    payBtn.disabled = selectedIds.length === 0;

                    // Row highlight
                    this.closest('.installment-card').style.borderColor = this.checked ? '#12342b' :
                        'rgba(24, 53, 45, 0.07)';
                    this.closest('.installment-card').style.background = this.checked ? '#f0f4f1' :
                        '#fbf8f2';
                });
            });

            payBtn.addEventListener('click', function() {
                modalAmt.innerText = totalAmount.toLocaleString();
                modalAmtBtn.innerText = totalAmount.toLocaleString();
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();
            });

            document.getElementById('initiate-razorpay').addEventListener('click', function() {
                this.disabled = true;
                this.innerText = 'Processing...';

                // AJAX to create order
                fetch("{{ route('student.create.order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            installment_ids: selectedIds,
                            amount: totalAmount
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const options = {
                                "key": data.razorpay_key,
                                "amount": data.order.amount,
                                "currency": "INR",
                                "name": "Suddhananda ERP",
                                "description": "Fee Payment",
                                "order_id": data.order.id,
                                "handler": function(response) {
                                    verifyPayment(response);
                                },
                                "prefill": {
                                    "name": "{{ $studentName }}",
                                    "email": "{{ $student->email ?? '' }}",
                                    "contact": "{{ $student->phone ?? '' }}"
                                },
                                "theme": {
                                    "color": "#12342b"
                                }
                            };
                            const rzp1 = new Razorpay(options);
                            rzp1.open();
                        } else {
                            alert(data.message || 'Failed to initiate payment.');
                            location.reload();
                        }
                    });
            });

            function verifyPayment(response) {
                fetch("{{ route('student.verify.payment') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            razorpay_payment_id: response.razorpay_payment_id,
                            razorpay_order_id: response.razorpay_order_id,
                            razorpay_signature: response.razorpay_signature,
                            installment_ids: selectedIds
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert('Payment successful!');
                            location.reload();
                        } else {
                            alert(data.message || 'Payment verification failed.');
                        }
                    });
            }
        });
    </script>
@endsection

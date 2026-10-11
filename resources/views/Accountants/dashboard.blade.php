@extends('admin_layout')

@section('content')
    <div class="nxl-content without-header nxl-full-content">
        <div class="main-content d-flex">
            @include('Accountants.sidebar')

            <div class="content-area" data-scrollbar-target="#psScrollbarInit">
                <div class="content-area-header bg-white sticky-top">
                    <h5 class="fw-bold mb-0 me-4">
                        <span class="d-block mb-2">Finance Dashboard</span>
                        <span class="fs-12 fw-normal text-muted">
                            Monitor income, expenses, trends, and recent accounting activity for this tenant.
                        </span>
                    </h5>
                    <div class="page-header-right ms-auto">
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper flex-wrap">
                            <a href="{{ route('income-records.create') }}" class="btn btn-primary">
                                <i class="feather-plus me-2"></i>Add Income
                            </a>
                            <a href="{{ route('expense-records.create') }}" class="btn btn-outline-danger">
                                <i class="feather-minus me-2"></i>Add Expense
                            </a>
                            <a href="{{ route('ledgers.index') }}" class="btn btn-outline-secondary">
                                <i class="feather-book-open me-2"></i>View Ledgers
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-area-body">
                    @include('elements.flash')

                    <div class="row g-4 mb-4">
                        <div class="col-xxl-6">
                            <div class="card border-0 overflow-hidden h-100" style="background: linear-gradient(135deg, #123458 0%, #1f5f8b 45%, #4b8db8 100%);">
                                <div class="card-body p-4 p-lg-5 text-white position-relative">
                                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                                        <div>
                                            <span class="text-uppercase small fw-semibold opacity-75">Net Position</span>
                                            <h2 class="fw-bold mt-2 mb-2">Rs {{ number_format($stats['netBalance'] ?? 0, 2) }}</h2>
                                            <p class="mb-0 opacity-75">
                                                {{ ($stats['netBalance'] ?? 0) >= 0 ? 'Overall surplus across recorded transactions.' : 'Overall spend is ahead of income right now.' }}
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-light text-dark px-3 py-2">
                                                This Month: Rs {{ number_format($stats['monthlyNet'] ?? 0, 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-4">
                                        <div class="col-sm-4">
                                            <div class="rounded-3 p-3" style="background: rgba(255,255,255,0.14);">
                                                <div class="fs-12 text-uppercase opacity-75 mb-1">Total Income</div>
                                                <div class="fw-bold fs-5">Rs {{ number_format($stats['totalIncome'] ?? 0, 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="rounded-3 p-3" style="background: rgba(255,255,255,0.14);">
                                                <div class="fs-12 text-uppercase opacity-75 mb-1">Total Expense</div>
                                                <div class="fw-bold fs-5">Rs {{ number_format($stats['totalExpense'] ?? 0, 2) }}</div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="rounded-3 p-3" style="background: rgba(255,255,255,0.14);">
                                                <div class="fs-12 text-uppercase opacity-75 mb-1">Transactions</div>
                                                <div class="fw-bold fs-5">{{ $stats['transactionsThisMonth'] ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-6">
                            <div class="row g-3 h-100">
                                <div class="col-md-6">
                                    <div class="card stretch stretch-full h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fs-12 text-muted text-uppercase mb-2">Today Income</div>
                                                    <h4 class="fw-bold text-success mb-1">Rs {{ number_format($stats['todayIncome'] ?? 0, 2) }}</h4>
                                                    <div class="fs-12 text-muted">Current day receipts</div>
                                                </div>
                                                <span class="avatar-text avatar-md bg-soft-success text-success">
                                                    <i class="feather-trending-up"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card stretch stretch-full h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fs-12 text-muted text-uppercase mb-2">Today Expense</div>
                                                    <h4 class="fw-bold text-danger mb-1">Rs {{ number_format($stats['todayExpense'] ?? 0, 2) }}</h4>
                                                    <div class="fs-12 text-muted">Current day spending</div>
                                                </div>
                                                <span class="avatar-text avatar-md bg-soft-danger text-danger">
                                                    <i class="feather-trending-down"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card stretch stretch-full h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fs-12 text-muted text-uppercase mb-2">Income Heads</div>
                                                    <h4 class="fw-bold mb-1">{{ $stats['incomeHeadsCount'] ?? 0 }}</h4>
                                                    <div class="fs-12 text-muted">Configured income categories</div>
                                                </div>
                                                <span class="avatar-text avatar-md bg-soft-primary text-primary">
                                                    <i class="feather-layers"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card stretch stretch-full h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fs-12 text-muted text-uppercase mb-2">Expense Heads</div>
                                                    <h4 class="fw-bold mb-1">{{ $stats['expenseHeadsCount'] ?? 0 }}</h4>
                                                    <div class="fs-12 text-muted">Configured expense categories</div>
                                                </div>
                                                <span class="avatar-text avatar-md bg-soft-warning text-warning">
                                                    <i class="feather-grid"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-xl-8">
                            <div class="card stretch stretch-full h-100">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <h6 class="mb-1">Income vs Expense Trend</h6>
                                            <span class="fs-12 text-muted">Last six completed months based on recorded transactions.</span>
                                        </div>
                                        <span class="badge bg-soft-primary text-primary">Dynamic</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div style="height: 320px;">
                                        <canvas id="financeTrendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4">
                            <div class="card stretch stretch-full h-100">
                                <div class="card-header">
                                    <h6 class="mb-1">Monthly Snapshot</h6>
                                    <span class="fs-12 text-muted">Current month performance at a glance.</span>
                                </div>
                                <div class="card-body">
                                    <div class="border rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Month Income</span>
                                            <strong class="text-success">Rs {{ number_format($stats['thisMonthIncome'] ?? 0, 2) }}</strong>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            @php
                                                $monthTotal = max(1, ($stats['thisMonthIncome'] ?? 0) + ($stats['thisMonthExpense'] ?? 0));
                                                $incomePercent = (($stats['thisMonthIncome'] ?? 0) / $monthTotal) * 100;
                                            @endphp
                                            <div class="progress-bar bg-success" style="width: {{ $incomePercent }}%"></div>
                                        </div>
                                    </div>

                                    <div class="border rounded-3 p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Month Expense</span>
                                            <strong class="text-danger">Rs {{ number_format($stats['thisMonthExpense'] ?? 0, 2) }}</strong>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-danger" style="width: {{ 100 - $incomePercent }}%"></div>
                                        </div>
                                    </div>

                                    <div class="rounded-3 p-3" style="background: #f5f8fc;">
                                        <div class="fs-12 text-muted text-uppercase mb-2">Net This Month</div>
                                        <h4 class="fw-bold mb-1 {{ ($stats['monthlyNet'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                            Rs {{ number_format($stats['monthlyNet'] ?? 0, 2) }}
                                        </h4>
                                        <span class="fs-12 text-muted">Based on current month income and expense records.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-xl-6">
                            <div class="card stretch stretch-full h-100">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Recent Income</h6>
                                            <span class="fs-12 text-muted">Latest credited records in this tenant.</span>
                                        </div>
                                        <a href="{{ route('income-records.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Head</th>
                                                    <th>Date</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentIncome as $record)
                                                    <tr class="single-item">
                                                        <td>
                                                            <div class="fw-semibold">{{ $record->incomeHead->name ?? 'N/A' }}</div>
                                                            <div class="fs-12 text-muted">{{ \Illuminate\Support\Str::limit($record->description, 36) ?: 'No description' }}</div>
                                                        </td>
                                                        <td>{{ optional($record->date)->format('d M Y') }}</td>
                                                        <td class="text-end fw-bold text-success">Rs {{ number_format($record->amount, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4 text-muted">No income records yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="card stretch stretch-full h-100">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">Recent Expenses</h6>
                                            <span class="fs-12 text-muted">Latest debited records in this tenant.</span>
                                        </div>
                                        <a href="{{ route('expense-records.index') }}" class="btn btn-sm btn-outline-danger">View All</a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Head</th>
                                                    <th>Date</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($recentExpenses as $record)
                                                    <tr class="single-item">
                                                        <td>
                                                            <div class="fw-semibold">{{ $record->expenseHead->name ?? 'N/A' }}</div>
                                                            <div class="fs-12 text-muted">{{ \Illuminate\Support\Str::limit($record->description, 36) ?: 'No description' }}</div>
                                                        </td>
                                                        <td>{{ optional($record->date)->format('d M Y') }}</td>
                                                        <td class="text-end fw-bold text-danger">Rs {{ number_format($record->amount, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4 text-muted">No expense records yet.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-xl-6">
                            <div class="card stretch stretch-full h-100">
                                <div class="card-header">
                                    <h6 class="mb-1">Top Income Heads</h6>
                                    <span class="fs-12 text-muted">Highest-contributing income categories.</span>
                                </div>
                                <div class="card-body">
                                    @forelse ($topIncomeHeads as $head)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <div>
                                                <div class="fw-semibold">{{ $head->incomeHead->name ?? 'Unknown Head' }}</div>
                                                <div class="fs-12 text-muted">Income category performance</div>
                                            </div>
                                            <span class="fw-bold text-success">Rs {{ number_format($head->total_amount, 2) }}</span>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">No income head data available.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6">
                            <div class="card stretch stretch-full h-100">
                                <div class="card-header">
                                    <h6 class="mb-1">Top Expense Heads</h6>
                                    <span class="fs-12 text-muted">Highest-spend expense categories.</span>
                                </div>
                                <div class="card-body">
                                    @forelse ($topExpenseHeads as $head)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <div>
                                                <div class="fw-semibold">{{ $head->expenseHead->name ?? 'Unknown Head' }}</div>
                                                <div class="fs-12 text-muted">Expense category utilization</div>
                                            </div>
                                            <span class="fw-bold text-danger">Rs {{ number_format($head->total_amount, 2) }}</span>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted">No expense head data available.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const trendCanvas = document.getElementById('financeTrendChart');

        if (trendCanvas) {
            new Chart(trendCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [{
                        label: 'Income',
                        data: @json($incomeTrend),
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.10)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2
                    }, {
                        label: 'Expense',
                        data: @json($expenseTrend),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rs ' + value;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection

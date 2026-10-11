@extends('studentadmin_layout')

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    @php
        $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
        $studentName = $studentName !== '' ? $studentName : 'Student';
        $issuedCount = $bookIssues->where('status', 'issued')->count();
        $returnedCount = $bookIssues->where('status', 'returned')->count();
        $overdueCount = $bookIssues
            ->filter(function ($book) {
                return $book->status === 'issued' &&
                    $book->due_date &&
                    \Carbon\Carbon::parse($book->due_date)->isPast();
            })
            ->count();
        $totalFine = $bookIssues->sum('fine_amount');
        $nextDueBook = $bookIssues
            ->filter(fn($book) => $book->status === 'issued' && $book->due_date)
            ->sortBy('due_date')
            ->first();
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

        .library-shell {
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
            font-size: 28px;
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
        .book-card,
        .note-card {
            border-radius: 18px;
            padding: 16px;
            background: linear-gradient(180deg, #faf7f1 0%, #edf4ee 100%);
            border: 1px solid rgba(24, 53, 45, 0.07);
        }

        .profile-label,
        .note-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #7b8b85;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .profile-value,
        .note-title {
            font-size: 15px;
            font-weight: 700;
            color: #18352d;
            line-height: 1.45;
        }

        .book-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .book-card {
            background: #fbf8f2;
            display: grid;
            grid-template-columns: minmax(0, 1.7fr) repeat(4, minmax(110px, 0.75fr));
            gap: 14px;
            align-items: center;
        }

        .book-main {
            min-width: 0;
        }

        .book-title {
            font-size: 15px;
            font-weight: 800;
            color: #18352d;
            margin-bottom: 4px;
            line-height: 1.35;
        }

        .book-sub {
            font-size: 12px;
            color: #687872;
            line-height: 1.55;
        }

        .book-stack {
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

        .status-pill.issued {
            background: #fff3dd;
            color: #8a5f17;
        }

        .status-pill.returned {
            background: #eaf5ee;
            color: #1f5d46;
        }

        .status-pill.overdue {
            background: #fde9e5;
            color: #a04231;
        }

        .fine-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: #85551a;
            background: #f6e7ce;
            border-radius: 999px;
            padding: 8px 10px;
            white-space: nowrap;
        }

        .no-fine {
            font-size: 13px;
            color: #8a9a94;
            font-weight: 700;
        }

        .empty-state {
            border-radius: 22px;
            padding: 44px 24px;
            text-align: center;
            background: linear-gradient(180deg, #faf7f1 0%, #f1f5ef 100%);
            border: 1px dashed rgba(24, 53, 45, 0.12);
        }

        .empty-state i {
            font-size: 34px;
            color: #8fa49b;
            margin-bottom: 14px;
        }

        .empty-title {
            font-size: 19px;
            font-weight: 800;
            color: #18352d;
            margin-bottom: 6px;
        }

        .empty-copy,
        .note-copy {
            margin: 0;
            color: #677973;
            font-size: 13px;
            line-height: 1.7;
        }

        @media (max-width: 1100px) {

            .hero-grid,
            .stats-grid,
            .profile-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .book-card {
                grid-template-columns: minmax(0, 1.4fr) repeat(2, minmax(110px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .library-shell {
                padding: 16px;
            }

            .hero-panel,
            .panel-card,
            .stat-card {
                border-radius: 20px;
            }

            .hero-grid,
            .summary-grid,
            .stats-grid,
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .panel-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .book-card {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="nxl-content">
        <div class="library-shell">
            <section class="hero-panel">
                <div class="hero-grid">
                    <div>
                        <span class="eyebrow">Student Library</span>
                        <h1 class="hero-title">Keep track of issued books with clarity,
                            {{ $student->first_name ?? 'Student' }}.</h1>
                        <p class="hero-copy">
                            Review your issued books, due dates, return status, and library fines from one simpler
                            student-friendly screen.
                        </p>

                        <div class="hero-chips">
                            <span class="hero-chip">Student: {{ $studentName }}</span>
                            <span class="hero-chip">Reg No: {{ $student->admission_number ?? 'N/A' }}</span>
                            <span class="hero-chip">Program: {{ $student->stream_name ?? 'Not assigned' }}</span>
                            <span class="hero-chip">Semester: {{ $student->class_name ?? 'Not assigned' }}</span>
                        </div>
                    </div>

                    <div class="hero-side">
                        <div class="summary-panel">
                            <div class="summary-grid">
                                <div class="summary-item">
                                    <div class="summary-label">Issued Books</div>
                                    <div class="summary-value">{{ $issuedCount }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Returned</div>
                                    <div class="summary-value">{{ $returnedCount }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Overdue</div>
                                    <div class="summary-value">{{ $overdueCount }}</div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Fine Total</div>
                                    <div class="summary-value">Rs {{ number_format($totalFine, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="due-panel">
                            <div class="due-label">Next Return Reminder</div>
                            <div class="due-value">
                                {{ $nextDueBook ? \Carbon\Carbon::parse($nextDueBook->due_date)->format('d M Y') : 'No due date' }}
                            </div>
                            <div class="due-copy">
                                @if ($nextDueBook)
                                    {{ $nextDueBook->title }} is the next active issue due for return.
                                @else
                                    No active issued book with a pending return date is available right now.
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Active Issues</div>
                        <div class="stat-icon"><i class="feather-book-open"></i></div>
                    </div>
                    <div class="stat-value">{{ $issuedCount }}</div>
                    <div class="stat-copy">Books that are still issued under this student account.</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Returned Books</div>
                        <div class="stat-icon"><i class="feather-check-circle"></i></div>
                    </div>
                    <div class="stat-value">{{ $returnedCount }}</div>
                    <div class="stat-copy">Books that have already been returned and cleared by the library.</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Overdue Books</div>
                        <div class="stat-icon"><i class="feather-alert-circle"></i></div>
                    </div>
                    <div class="stat-value">{{ $overdueCount }}</div>
                    <div class="stat-copy">Books that have crossed the due date and may need immediate action.</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <div class="stat-label">Fine Balance</div>
                        <div class="stat-icon"><i class="feather-credit-card"></i></div>
                    </div>
                    <div class="stat-value">Rs {{ number_format($totalFine, 0) }}</div>
                    <div class="stat-copy">Current fine amount recorded against all library book issues.</div>
                </article>
            </section>

            <section class="panel-card">
                <div class="panel-head">
                    <div>
                        <div class="panel-kicker">Issued Books</div>
                        <h2 class="panel-title">Library Records</h2>
                        <p class="panel-copy">A cleaner list of issued, returned, and overdue books with due dates and fine
                            visibility.</p>
                    </div>
                </div>

                @if ($bookIssues->count() > 0)
                    <div class="book-list">
                        @foreach ($bookIssues as $book)
                            @php
                                $isOverdue =
                                    $book->status === 'issued' &&
                                    $book->due_date &&
                                    \Carbon\Carbon::parse($book->due_date)->isPast();
                                $statusClass = $isOverdue ? 'overdue' : $book->status;
                                $statusLabel = $isOverdue ? 'Overdue' : ucfirst($book->status);
                            @endphp
                            <div class="book-card">
                                <div class="book-main">
                                    <div class="book-title">{{ $book->title }}</div>
                                    <div class="book-sub">
                                        Author: {{ $book->author ?: 'Not available' }}<br>
                                        ISBN: {{ $book->isbn ?: 'Not available' }}
                                    </div>
                                </div>

                                <div class="book-stack">
                                    <div class="stack-label">Issue / Due</div>
                                    <div class="stack-value">
                                        {{ $book->issue_date ? \Carbon\Carbon::parse($book->issue_date)->format('d M Y') : 'N/A' }}<br>
                                        <span style="color:#687872;">Due:
                                            {{ $book->due_date ? \Carbon\Carbon::parse($book->due_date)->format('d M Y') : 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="book-stack">
                                    <div class="stack-label">Return Date</div>
                                    <div class="stack-value">
                                        {{ $book->return_date ? \Carbon\Carbon::parse($book->return_date)->format('d M Y') : 'Not returned' }}
                                    </div>
                                </div>

                                <div class="book-stack">
                                    <div class="stack-label">Fine</div>
                                    <div class="stack-value">
                                        @if ($book->fine_amount > 0)
                                            <span class="fine-pill">Rs {{ number_format($book->fine_amount, 2) }}</span>
                                        @else
                                            <span class="no-fine">No fine</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="book-stack">
                                    <div class="stack-label">Status</div>
                                    <div class="stack-value">
                                        <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="feather-book"></i>
                        <div class="empty-title">No library records found</div>
                        <p class="empty-copy">This student does not currently have any issued or returned book records in
                            the library module.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>

@endsection

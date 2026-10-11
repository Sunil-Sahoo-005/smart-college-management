<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
        }

        .header p {
            margin: 3px 0;
        }

        .summary-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .summary-table td {
            border: 1px solid #ddd;
            text-align: center;
            padding: 12px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }

        .present { background: #d4edda; }
        .absent { background: #f8d7da; }
        .late { background: #fff3cd; }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.report-table th {
            background: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            font-weight: bold;
            text-align: left;
        }

        table.report-table td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        .status-present { color: green; font-weight: bold; }
        .status-absent { color: red; font-weight: bold; }
        .status-late { color: orange; font-weight: bold; }

        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>

<div class="header">
    <h2>College Attendance Report</h2>
    <p>Date: {{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}</p>
</div>


<!-- Summary Section -->
<table class="summary-table">
    <tr>
        <td class="present">
            <div class="summary-value">{{ $stats['present'] ?? 0 }}</div>
            <div class="summary-title">Present</div>
        </td>

        <td class="absent">
            <div class="summary-value">{{ $stats['absent'] ?? 0 }}</div>
            <div class="summary-title">Absent</div>
        </td>

        <td class="late">
            <div class="summary-value">{{ ($stats['late'] ?? 0) + ($stats['half_day'] ?? 0) }}</div>
            <div class="summary-title">Late / Half Day</div>
        </td>
    </tr>
</table>


<!-- Attendance Table -->
<table class="report-table">
    <thead>
        <tr>
            <th>Roll No</th>
            <th>Student</th>
            <th>Course</th>
            <th>Branch</th>
            <th>Semester</th>
            <th>Section</th>
            <th>Period</th>
            <th>Status</th>
            <th>Remarks</th>
            <th>Marked By</th>
            <th>Date</th>
        </tr>
    </thead>

    <tbody>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ $attendance->enrollment->student->roll_number ?? '' }}</td>

            <td>
                {{ $attendance->enrollment->student->first_name }}
                {{ $attendance->enrollment->student->last_name }}
            </td>

            <td>{{ $attendance->enrollment->course->name ?? '' }}</td>
            <td>{{ $attendance->enrollment->stream->name ?? '' }}</td>
            <td>{{ $attendance->enrollment->class->name ?? '' }}</td>
            <td>{{ $attendance->enrollment->section->name ?? '' }}</td>
            <td>{{ $attendance->period->name ?? '' }}</td>

            <td class="status-{{ $attendance->status }}">
                {{ ucfirst($attendance->status) }}
            </td>

            <td>{{ $attendance->remarks ?? '' }}</td>
            <td>{{ $attendance->markedBy->name ?? '' }}</td>
            <td>{{ $attendance->date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


<div class="footer">
    Generated on: {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>
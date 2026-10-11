<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HrAttendanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    public function __construct(private Collection $attendances)
    {
    }

    public function collection()
    {
        return $this->attendances->map(function ($attendance) {
            return [
                $attendance->staff?->employee_id ?? '',
                trim(($attendance->staff?->first_name ?? '') . ' ' . ($attendance->staff?->last_name ?? '')),
                $attendance->staff?->department?->name ?? '',
                $attendance->staff?->designation?->name ?? '',
                $attendance->attendance_date?->format('Y-m-d'),
                $attendance->check_in ?? '',
                $attendance->check_out ?? '',
                $attendance->working_minutes ?? 0,
                ucfirst(str_replace('_', ' ', $attendance->status ?? '')),
                $attendance->source ?? '',
                $attendance->remark ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Employee ID',
            'Employee Name',
            'Department',
            'Designation',
            'Attendance Date',
            'Check In',
            'Check Out',
            'Working Minutes',
            'Status',
            'Source',
            'Remark',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

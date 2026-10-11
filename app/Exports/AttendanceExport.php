<?php

namespace App\Exports;

use App\Models\StudentAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    public function collection()
    {
        return $this->attendances->map(function ($attendance) {
            return [
                $attendance->enrollment->student->roll_number ?? '',
                $attendance->enrollment->student->first_name . ' ' . $attendance->enrollment->student->last_name,
                $attendance->enrollment->course->name ?? '',
                $attendance->enrollment->stream->name ?? '',
                $attendance->enrollment->class->name ?? '',
                $attendance->enrollment->section->name ?? '',
                $attendance->period->name ?? '',
                ucfirst($attendance->status),
                $attendance->remarks ?? '',
                $attendance->markedBy->name ?? '',
                $attendance->date,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Roll No', 'Student Name', 'Course', 'Branch', 'Semester', 'Section', 
            'Period', 'Status', 'Remarks', 'Marked By', 'Date'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style first row (headings)
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

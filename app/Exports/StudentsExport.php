<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
            'Admission Number',
            'Roll Number',
            'First Name',
            'Last Name',
            'Gender',
            'Date of Birth',
            'Blood Group',
            'Category',
            'Status',
            'Academic Year',
            'Stream',
            'Class',
            'Contact Number',
            'Email',
            'Address',
        ];
    }

    public function map($student): array
    {
        $activeEnrollment = $student->enrollments->where('status', 'active')->first();

        return [
            $student->admission_number ?? 'N/A',
            $student->roll_number ?? 'N/A',
            $student->first_name ?? '',
            $student->last_name ?? '',
            ucfirst($student->gender ?? 'N/A'),
            $student->dob ? $student->dob->format('d-m-Y') : 'N/A',
            $student->blood_group ?? 'N/A',
            strtoupper($student->category ?? 'N/A'),
            ucfirst($student->status ?? 'N/A'),
            $activeEnrollment ? $activeEnrollment->academicYear->name ?? 'N/A' : 'N/A',
            $activeEnrollment ? $activeEnrollment->stream->name ?? 'N/A' : 'N/A',
            $activeEnrollment ? $activeEnrollment->class->name ?? 'N/A' : 'N/A',
            $student->contact_number ?? 'N/A',
            $student->email ?? 'N/A',
            $student->address ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true],
            ],
        ];
    }
}
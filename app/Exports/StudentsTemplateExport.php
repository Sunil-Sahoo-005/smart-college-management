<?php
namespace App\Exports;
 
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
 
class StudentsTemplateExport implements
    FromArray,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    public function title(): string
    {
        return 'Students';
    }
 
    public function headings(): array
    {
        return [
            // Required fields marked with *
            'first_name *',
            'last_name *',
            'gender',
            'dob',
            'blood_group',
            'aadhaar',
            'category',
            'religion',
            'nationality',
            'roll_number',
            'status *',
            'emergency_contact_name',
            'emergency_contact_phone',
            'medical_conditions',
            'allergies',
            'academic_year_id *',
            'stream_id *',
            'class_id *',
            'section_id *',
            'student_type',
            'residence_type',
            'dateof_admission',
            'father_name',
            'mother_name',
            'parent_phone',
            'parent_email',
            'address',
            'city',
            'state',
            'pincode',
        ];
    }
 
    public function array(): array
    {
        // One example row — users should delete this before importing
        return [
            [
                'Rahul',           // first_name
                'Sharma',          // last_name
                'male',            // gender
                '2005-04-15',      // dob  (YYYY-MM-DD)
                'B+',              // blood_group
                '123456789012',    // aadhaar
                'obc',             // category
                'Hindu',           // religion
                'Indian',          // nationality
                '101',             // roll_number
                'active',          // status
                'Suresh Sharma',   // emergency_contact_name
                '9876543210',      // emergency_contact_phone
                '',                // medical_conditions
                '',                // allergies
                '1',               // academic_year_id
                '2',               // stream_id
                '3',               // class_id
                '1',               // section_id
                'regular',         // student_type
                'day_scholar',     // residence_type
                '2024-06-01',      // dateof_admission
                'Suresh Sharma',   // father_name
                'Priya Sharma',    // mother_name
                '9876543210',      // parent_phone
                'parent@mail.com', // parent_email
                '123, MG Road',    // address
                'Bhubaneswar',     // city
                'Odisha',          // state
                '751001',          // pincode
            ],
        ];
    }
 
    public function columnWidths(): array
    {
        return [
            'A' => 18, 'B' => 18, 'C' => 12, 'D' => 16, 'E' => 14,
            'F' => 18, 'G' => 12, 'H' => 14, 'I' => 14, 'J' => 14,
            'K' => 14, 'L' => 22, 'M' => 22, 'N' => 24, 'O' => 18,
            'P' => 20, 'Q' => 16, 'R' => 14, 'S' => 14, 'T' => 14,
            'U' => 16, 'V' => 18, 'W' => 18, 'X' => 18, 'Y' => 16,
            'Z' => 24, 'AA'=> 30, 'AB'=> 14, 'AC'=> 14, 'AD'=> 12,
        ];
    }
 
    public function styles(Worksheet $sheet): array
    {
        // Header row style
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'name' => 'Arial'],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
            ],
            // Example data row
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '6B7280'], 'name' => 'Arial'],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F0FDF4'],
                ],
            ],
        ];
    }
}
 
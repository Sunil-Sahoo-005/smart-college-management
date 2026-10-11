<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classe;
use App\Models\Course;
use App\Models\CourseStream;
use App\Models\ClassStreamMapping;
use App\Models\Section;
use App\Models\Stream;
use App\Models\Student;
use App\Models\StudentClassEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentImportController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // SHOW IMPORT PAGE
    // ─────────────────────────────────────────────────────────────
   public function showImport()
{
    $tenantId      = auth()->user()->tenant_id;
    $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderBy('name', 'desc')->get();
    $courses       = Course::where('tenant_id', $tenantId)->orderBy('name')->get();
    $streams       = Stream::where('tenant_id', $tenantId)->orderBy('name')->get();
    $classes       = Classe::where('tenant_id', $tenantId)->orderBy('name')->get();
    
    // ✅ Keep as Collection (remove toArray)
    $sections = Section::where('tenant_id', $tenantId)
        ->select('name')
        ->distinct()
        ->orderBy('name')
        ->pluck('name');  // This is a Collection
    
    return view('Academics.student.import', compact('academicYears', 'courses', 'streams', 'classes', 'sections'));
}

    // ─────────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE (dynamic — live data from DB)
    // ─────────────────────────────────────────────────────────────
//     public function downloadTemplate()
//     {
//         $tenantId      = auth()->user()->tenant_id;
//         $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderBy('name', 'desc')->pluck('name')->toArray();
//         $courses       = Course::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
//         $streams       = Stream::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
//         $classes       = Classe::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
//         // $sections      = Section::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
//         $sections = Section::where('tenant_id', $tenantId)
//         ->distinct('name')  // Sirf unique names
//         ->orderBy('name')
//         ->pluck('name')
//         ->toArray();
//         $templatePath = public_path('templates/student_import_template.xlsx');
//         $spreadsheet  = IOFactory::load($templatePath);

//         // 1. Insert columns in the "Student Import" sheet
//         $dataSheet = $spreadsheet->getSheetByName('Student Import');
        
//         // Insert Sub Caste at column K (after Category J)
//         $dataSheet->insertNewColumnBefore('K', 1);
//         $dataSheet->setCellValue('K1', 'Sub Caste');
//         $dataSheet->getStyle('K1')->getFont()->setBold(true);

//         // Insert Course at column S (after Roll Number R)
//         // Note: after inserting K, the old R becomes S. So inserting before T (the old S).
//         $dataSheet->insertNewColumnBefore('S', 1);
//         $dataSheet->setCellValue('S1', 'Course*');
//         $dataSheet->getStyle('S1')->getFont()->setBold(true);

//         $lookupSheet  = $spreadsheet->getSheetByName('_Lookups');

//         // Overwrite dynamic lookup columns
//         $dynamic = [
//             'L' => $academicYears,
//             'M' => $streams,
//             'N' => $classes,
//             'O' => $sections,
//             'P' => $courses,
//         ];

//         foreach ($dynamic as $col => $values) {
//             for ($r = 2; $r <= 200; $r++) {
//                 $lookupSheet->getCell("{$col}{$r}")->setValue('');
//             }
//             foreach ($values as $i => $v) {
//                 $lookupSheet->getCell("{$col}" . ($i + 2))->setValue($v);
//             }
//         }

//         // Update data-validation ranges for these columns
//         $colEnds   = [
//             'T' => max(count($academicYears), 1) + 1, // Old L moved to T due to two insertions
//             'U' => max(count($streams),       1) + 1, // Old M moved to U
//             'V' => max(count($classes),       1) + 1, // Old N moved to V
//             'W' => max(count($sections),      1) + 1, // Old O moved to W
//             'S' => max(count($courses),       1) + 1, // New S
//         ];
        
//         // Map lookup columns to data sheet columns
//         // Lookup L -> Data T (Academic Year)
//         // Lookup M -> Data U (Stream)
//         // Lookup N -> Data V (Class)
//         // Lookup O -> Data W (Section)
//         // Lookup P -> Data S (Course)
        
//  $mapping = [
//                 'L' => 'T',  // Academic Year
//                 'M' => 'U',  // Stream
//                 'N' => 'V',  // Class
//                 'O' => 'W',  // Section
//                 'P' => 'S',  // Course
//             ];

//         foreach ($dataSheet->getDataValidationCollection() as $dv) {
//             $f = (string) $dv->getFormula1();
//             foreach ($mapping as $lkpCol => $dataCol) {
//                 if (str_contains($f, "_Lookups!\${$lkpCol}\$")) {
//                     $end = $colEnds[$dataCol];
//                     $dv->setFormula1("_Lookups!\${$lkpCol}\$2:\${$lkpCol}\${$end}");
//                 }
//             }
//         }

//         // Add explicit data validation for the new Course column (S) since it was newly inserted
//         $courseValidation = $dataSheet->getDataValidation('S2:S500');
//         $courseValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
//         $courseValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
//         $courseValidation->setAllowBlank(true);
//         $courseValidation->setShowInputMessage(true);
//         $courseValidation->setShowErrorMessage(true);
//         $courseValidation->setShowDropDown(true);
//         $courseValidation->setErrorTitle('Input error');
//         $courseValidation->setError('Value is not in list.');
//         $courseValidation->setPromptTitle('Pick from list');
//         $courseValidation->setPrompt('Please pick a course from the drop-down list.');
//         $courseValidation->setFormula1("_Lookups!\$P\$2:\$P\${$colEnds['S']}");

//         $writer   = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
//         $filename = 'student_import_template_' . date('Ymd') . '.xlsx';

//         return response()->streamDownload(function () use ($writer) {
//             $writer->save('php://output');
//         }, $filename, [
//             'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
//             'Cache-Control' => 'max-age=0',
//         ]);
//     }

public function downloadTemplate()
{
    $tenantId      = auth()->user()->tenant_id;
    $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderBy('name', 'desc')->pluck('name')->toArray();
    $courses       = Course::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
    $streams       = Stream::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
    $classes       = Classe::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
    $sections      = Section::where('tenant_id', $tenantId)->select('name')->distinct()->orderBy('name')->pluck('name')->toArray();

    $templatePath = public_path('templates/student_import_template.xlsx');
    $spreadsheet  = IOFactory::load($templatePath);

    $dataSheet = $spreadsheet->getSheetByName('Student Import');
    $lookupSheet = $spreadsheet->getSheetByName('_Lookups');

    // Insert Sub Caste column
    $dataSheet->insertNewColumnBefore('K', 1);
    $dataSheet->setCellValue('K1', 'Sub Caste');
    $dataSheet->getStyle('K1')->getFont()->setBold(true);

    // Insert Course column
    $dataSheet->insertNewColumnBefore('S', 1);
    $dataSheet->setCellValue('S1', 'Course*');
    $dataSheet->getStyle('S1')->getFont()->setBold(true);

    // Update lookup data
    $dynamic = [
        'L' => $academicYears,
        'M' => $streams,
        'N' => $classes,
        'O' => $sections,
        'P' => $courses,
    ];

    foreach ($dynamic as $col => $values) {
        for ($r = 2; $r <= 200; $r++) {
            $lookupSheet->getCell("{$col}{$r}")->setValue('');
        }
        foreach ($values as $i => $v) {
            $lookupSheet->getCell("{$col}" . ($i + 2))->setValue($v);
        }
    }

    $colEnds = [
        'T' => max(count($academicYears), 1) + 1,
        'U' => max(count($streams), 1) + 1,
        'V' => max(count($classes), 1) + 1,
        'W' => max(count($sections), 1) + 1,
        'S' => max(count($courses), 1) + 1,
    ];

    $mapping = [
        'L' => 'T',
        'M' => 'U',
        'N' => 'V',
        'O' => 'W',
        'P' => 'S',
    ];

    // Update existing validations
    foreach ($dataSheet->getDataValidationCollection() as $dv) {
        $f = (string) $dv->getFormula1();
        foreach ($mapping as $lkpCol => $dataCol) {
            if (str_contains($f, "_Lookups!\${$lkpCol}\$")) {
                $end = $colEnds[$dataCol];
                $dv->setFormula1("_Lookups!\${$lkpCol}\$2:\${$lkpCol}\${$end}");
            }
        }
    }

    // Add validation for Course column
    $courseValidation = $dataSheet->getDataValidation('S2:S500');
    $courseValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $courseValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
    $courseValidation->setAllowBlank(true);
    $courseValidation->setShowDropDown(true);
    $courseValidation->setFormula1("_Lookups!\$P\$2:\$P\${$colEnds['S']}");

    // FIX: Clear validations from Branch Name (AF) and Account Type (AG)
    // Use setDataValidation with null instead of removeDataValidation
    for ($row = 2; $row <= 500; $row++) {
        $dataSheet->setDataValidation("AF{$row}", null);  // Clear Branch Name
        $dataSheet->setDataValidation("AG{$row}", null);  // Clear Account Type
    }

    // Add correct validation for Account Type column (AG)
    $accountTypeValidation = $dataSheet->getDataValidation('AG2:AG500');
    $accountTypeValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $accountTypeValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
    $accountTypeValidation->setAllowBlank(true);
    $accountTypeValidation->setShowDropDown(true);
    $accountTypeValidation->setErrorTitle('Invalid Account Type');
    $accountTypeValidation->setError('Please select savings or current');
    $accountTypeValidation->setPromptTitle('Account Type');
    $accountTypeValidation->setPrompt('Select account type from the list');
    $accountTypeValidation->setFormula1('"savings,current"');

    // Optional: Gender validation
    $genderValidation = $dataSheet->getDataValidation('C2:C500');
    $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
    $genderValidation->setAllowBlank(true);
    $genderValidation->setShowDropDown(true);
    $genderValidation->setFormula1('"male,female,other"');

    // Download
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $filename = 'student_import_template_' . date('Ymd') . '.xlsx';

    return response()->streamDownload(function () use ($writer) {
        $writer->save('php://output');
    }, $filename, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Cache-Control' => 'max-age=0',
    ]);
}
    // ─────────────────────────────────────────────────────────────
    // PROCESS IMPORT
    // ─────────────────────────────────────────────────────────────
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'import_file.required' => 'Please upload an Excel file.',
            'import_file.mimes'    => 'Only .xlsx or .xls files are accepted.',
            'import_file.max'      => 'File must not exceed 5 MB.',
        ]);

        $tenantId = auth()->user()->tenant_id;

        $path        = $request->file('import_file')->getRealPath();
        $spreadsheet = IOFactory::load($path);
        $sheet       = $spreadsheet->getSheetByName('Student Import') ?? $spreadsheet->getActiveSheet();
        $highestRow  = $sheet->getHighestDataRow();

        // Data starts at row 3 (row 1 = headers, row 2 = sample)
        if ($highestRow < 3) {
            return back()->with('error', 'No data found. Fill in student data from row 3 onwards (row 2 is the sample).');
        }

        // ── Build lookup maps: normalised-name → id ───────────────────────
        // We normalise by lowercasing + collapsing whitespace so minor
        // capitalisation / spacing differences in the sheet are forgiven.
       $academicYearMap = AcademicYear::where('tenant_id', $tenantId)
    ->get()
    ->mapWithKeys(fn($m) => [$this->normalise($m->name) => $m->id])
    ->toArray();  // ✅ plain array
$courseMap = Course::where('tenant_id', $tenantId)
    ->get()
    ->mapWithKeys(fn($m) => [$this->normalise($m->name) => $m->id])
    ->toArray();  // ✅ plain array
$streamMap = Stream::where('tenant_id', $tenantId)
    ->get()
    ->mapWithKeys(fn($m) => [$this->normalise($m->name) => $m->id])
    ->toArray();  // ✅ plain array

$classMap = Classe::where('tenant_id', $tenantId)
    ->get()
    ->mapWithKeys(fn($m) => [$this->normalise($m->name) => $m->id])
    ->toArray();  // ✅ plain array

$sectionMap = Section::where('tenant_id', $tenantId)
    ->get()
    ->mapWithKeys(fn($m) => [$this->normalise($m->name) => $m->id])
    ->toArray();  // ✅ plain array

        // ── Duplicate detection (DB + within-file) ────────────────────────
        $existingEmails  = Student::where('tenant_id', $tenantId)->pluck('email')->filter()->map('strtolower')->flip()->toArray();
        $existingAadhaar = Student::where('tenant_id', $tenantId)->pluck('aadhaar')->filter()->flip()->toArray();
        $fileEmails      = [];
        $fileAadhaar     = [];

        $errors   = [];
        $imported = 0;
        $skipped  = 0;

        for ($row = 3; $row <= $highestRow; $row++) {
            $raw = $this->readRow($sheet, $row);
            if ($this->isEmptyRow($raw)) { $skipped++; continue; }

            $rowErrors = $this->validateRow(
                $raw, $row,
                $academicYearMap, $courseMap, $streamMap, $classMap, $sectionMap,
                $existingEmails, $existingAadhaar,
                $fileEmails, $fileAadhaar
            );
            
            if (! empty($rowErrors)) {
                $errors[] = ['row' => $row, 'data' => $raw, 'messages' => $rowErrors];
                continue;
            }

            // Track within-file duplicates only after validation passes
            if ($raw['email'])   $fileEmails[$raw['email']]    = $row;
            if ($raw['aadhaar']) $fileAadhaar[$raw['aadhaar']] = $row;

            try {
                $student = $this->createStudent($raw, $tenantId, $courseMap, $streamMap);
                $this->createEnrollment(
                    $raw, $student, $tenantId,
                    $academicYearMap, $courseMap, $streamMap, $classMap, $sectionMap
                );

                // Add to seen sets so subsequent rows can detect duplicates
                if ($raw['email'])   $existingEmails[$raw['email']]    = true;
                if ($raw['aadhaar']) $existingAadhaar[$raw['aadhaar']] = true;

                $imported++;
            } catch (\Exception $e) {
                $errors[] = ['row' => $row, 'data' => $raw, 'messages' => ['Database error: ' . $e->getMessage()]];
            }
        }

        $summary = ['imported' => $imported, 'errors' => count($errors), 'skipped' => $skipped];
        if (! empty($errors)) session()->flash('import_errors', $errors);
        session()->flash('import_summary', $summary);

        $msg = "Import complete — {$imported} student(s) added.";
        if ($summary['errors'])  $msg .= " {$summary['errors']} row(s) had errors (see below).";
        if ($skipped)            $msg .= " {$skipped} blank row(s) skipped.";

        return redirect()->route('admin.students.import.show')
            ->with($imported > 0 ? 'success' : 'warning', $msg);
    }

    // ─────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Read one spreadsheet row into a named array.
     *
     * Column map (matches the uploaded template exactly):
     *   A  first_name          R  student_status
     *   B  last_name           S  course
     *   C  gender              T  roll_number
     *   D  dob                 U  academic_year
     *   E  blood_group         V  stream
     *   F  religion            W  class
     *   G  nationality         X  section
     *   H  aadhaar             Y  student_type
     *   I  email               Z  residence_type
     *   J  category            AA dateof_admission
     *   K  sub_caste           AB date_of_leaving
     *   L  physically_handicapped AC enrollment_status
     *   M  handicap_details    AD account_holder_name
     *   N  emergency_contact_name AE bank_name
     *   O  emergency_contact_phone AF branch_name
     *   P  medical_conditions  AG account_type
     *   Q  allergies           AH account_number
     *                          AI ifsc_code
     */
    private function readRow($sheet, int $row): array
    {
        // Helper: get raw cell value as trimmed string, handling dates & numbers
        $v = function (string $col) use ($sheet, $row): string {
            $cell  = $sheet->getCell("{$col}{$row}");
            $value = $cell->getValue();

            // Excel date serial → Y-m-d string
            if (
                $value !== null
                && is_numeric($value)
                && $cell->getDataType() === \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell)
            ) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            // DateTime object (openpyxl-style or cached)
            if ($value instanceof \DateTime) {
                return $value->format('Y-m-d');
            }

            return trim((string) ($value ?? ''));
        };

        return [
            // ── Personal ────────────────────────────────────────────────
            'first_name'              => $v('A'),
            'last_name'               => $v('B'),
            'gender'                  => strtolower($v('C')),
            'dob'                     => $v('D'),
            'blood_group'             => $v('E'),
            'religion'                => $v('F'),
            'nationality'             => $v('G'),
            'aadhaar'                 => preg_replace('/\D/', '', $v('H')),
            'email'                   => strtolower($v('I')),
            'category'                => strtolower($v('J')),
            'sub_caste'               => $v('K'),
            'physically_handicapped'  => strtolower($v('L')),
            'handicap_details'        => $v('M'),
            'emergency_contact_name'  => $v('N'),
            'emergency_contact_phone' => preg_replace('/\D/', '', $v('O')),
            'medical_conditions'      => $v('P'),
            'allergies'               => $v('Q'),
            // ── Status / Roll ───────────────────────────────────────────
            'student_status'          => strtolower($v('R')),
            'course'                  => $v('S'),
            'roll_number'             => $v('T'),
            // ── Enrollment (names — resolved to IDs in createEnrollment) ─
            'academic_year'           => $v('U'),
            'stream'                  => $v('V'),
            'class'                   => $v('W'),
            'section'                 => $v('X'),
            'student_type'            => strtolower($v('Y')),
            'residence_type'          => strtolower($v('Z')),
            'dateof_admission'        => $v('AA'),
            'date_of_leaving'         => $v('AB'),
            'enrollment_status'       => strtolower($v('AC')),
            // ── Bank ────────────────────────────────────────────────────
            'account_holder_name'     => $v('AD'),
            'bank_name'               => $v('AE'),
            'branch_name'             => $v('AF'),
            'account_type'            => strtolower($v('AG')),
            'account_number'          => $v('AH'),
            'ifsc_code'               => strtoupper($v('AI')),
        ];
    }

    private function isEmptyRow(array $raw): bool
    {
        return collect($raw)->every(fn($v) => $v === '' || $v === null);
    }

    /**
     * Normalise a name for lookup: lowercase + collapse whitespace.
     * This forgives minor differences like "Computer Science" vs "computer science".
     */
    private function normalise(string $value): string
    {
        return strtolower(preg_replace('/\s+/', ' ', trim($value)));
    }

    private function validateRow(
        array $raw, int $row,
        $ayMap, $courseMap, $streamMap, $classMap, $sectionMap,
        array &$existingEmails, array &$existingAadhaar,
        array &$fileEmails, array &$fileAadhaar
    ): array {
        $e = [];

        // ── Required text fields ─────────────────────────────────────────
        if (empty($raw['first_name']))              $e[] = 'First Name is required.';
        elseif (strlen($raw['first_name']) > 50)    $e[] = 'First Name must be ≤ 50 characters.';

        if (empty($raw['last_name']))               $e[] = 'Last Name is required.';
        elseif (strlen($raw['last_name']) > 50)     $e[] = 'Last Name must be ≤ 50 characters.';

        // ── Student status ────────────────────────────────────────────────
        if (empty($raw['student_status'])) {
            $e[] = 'Student Status is required.';
        } elseif (! in_array($raw['student_status'], ['active', 'discontinued', 'transfer_out', 'alumni'])) {
            $e[] = "Student Status '{$raw['student_status']}' is invalid. Use: active, discontinued, transfer_out, alumni.";
        }

        // ── Gender ───────────────────────────────────────────────────────
        if (! empty($raw['gender']) && ! in_array($raw['gender'], ['male', 'female', 'other'])) {
            $e[] = "Gender '{$raw['gender']}' is invalid. Use: male, female, other.";
        }

        // ── DOB ──────────────────────────────────────────────────────────
        if (! empty($raw['dob']) && ! $this->isValidDate($raw['dob'])) {
            $e[] = "DOB '{$raw['dob']}' is not a valid date. Use YYYY-MM-DD format.";
        }

        // ── Blood group ───────────────────────────────────────────────────
        if (! empty($raw['blood_group'])) {
            $bg = strtoupper(trim($raw['blood_group']));
            if (! in_array($bg, ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])) {
                $e[] = "Blood Group '{$raw['blood_group']}' is invalid. Use: A+, A-, B+, B-, AB+, AB-, O+, O-.";
            }
        }

        // ── Aadhaar ───────────────────────────────────────────────────────
        if (! empty($raw['aadhaar'])) {
            if (strlen($raw['aadhaar']) !== 12) {
                $e[] = "Aadhaar must be exactly 12 digits (got '{$raw['aadhaar']}').";
            } elseif (isset($existingAadhaar[$raw['aadhaar']])) {
                $e[] = "Aadhaar '{$raw['aadhaar']}' already exists in the system.";
            } elseif (isset($fileAadhaar[$raw['aadhaar']])) {
                $e[] = "Aadhaar '{$raw['aadhaar']}' is duplicated in this file (first seen row {$fileAadhaar[$raw['aadhaar']]}).";
            }
        }

        // ── Email ─────────────────────────────────────────────────────────
        if (! empty($raw['email'])) {
            if (! filter_var($raw['email'], FILTER_VALIDATE_EMAIL)) {
                $e[] = "Email '{$raw['email']}' is not a valid email address.";
            } elseif (isset($existingEmails[$raw['email']])) {
                $e[] = "Email '{$raw['email']}' already exists in the system.";
            } elseif (isset($fileEmails[$raw['email']])) {
                $e[] = "Email '{$raw['email']}' is duplicated in this file (first seen row {$fileEmails[$raw['email']]}).";
            }
        }

        // ── Category ─────────────────────────────────────────────────────
        if (! empty($raw['category']) && ! in_array($raw['category'], ['sc', 'st', 'obc', 'general', 'sebc', 'other'])) {
            $e[] = "Category '{$raw['category']}' is invalid. Use: sc, st, obc, general, sebc, other.";
        }

        // ── Physically handicapped ────────────────────────────────────────
        if (! empty($raw['physically_handicapped']) && ! in_array($raw['physically_handicapped'], ['yes', 'no'])) {
            $e[] = "Physically Handicapped must be 'yes' or 'no'.";
        }
        if ($raw['physically_handicapped'] === 'yes' && empty($raw['handicap_details'])) {
            $e[] = 'Handicap Details is required when Physically Handicapped is yes.';
        }

        // ── Emergency phone ───────────────────────────────────────────────
        if (! empty($raw['emergency_contact_phone'])) {
            $len = strlen($raw['emergency_contact_phone']);
            if ($len < 10 || $len > 15) {
                $e[] = "Emergency Contact Phone must be 10–15 digits (got {$len} digits).";
            }
        }

        // ── Enrollment — Course ──────────────────────────────────────────
        if (empty($raw['course'])) {
            $e[] = 'Course is required (column S).';
        } elseif (! isset($courseMap[$this->normalise($raw['course'])])) {
            $available = implode(', ', array_keys($courseMap));
            $e[] = "Course '{$raw['course']}' not found. Available: {$available}.";
        }

        // ── Enrollment — Academic Year ────────────────────────────────────
        if (empty($raw['academic_year'])) {
            $e[] = 'Academic Year is required (column U).';
        } elseif (! isset($ayMap[$this->normalise($raw['academic_year'])])) {
            // Show available options to help the user fix it
            // $available = implode(', ', array_keys($ayMap));
            $available = implode(', ', array_keys($ayMap->toArray()));
            $e[] = "Academic Year '{$raw['academic_year']}' not found. Available: {$available}.";
        }

        // ── Enrollment — Stream / Branch ──────────────────────────────────
        if (empty($raw['stream'])) {
            $e[] = 'Branch/Stream is required (column V).';
        } elseif (! isset($streamMap[$this->normalise($raw['stream'])])) {
            // $available = implode(', ', array_keys($streamMap));
            $available = implode(', ', array_keys($streamMap->toArray()));
            $e[] = "Branch '{$raw['stream']}' not found. Available: {$available}.";
        }

        // ── Enrollment — Class / Semester ─────────────────────────────────
        if (empty($raw['class'])) {
            $e[] = 'Semester/Class is required (column W).';
        } elseif (! isset($classMap[$this->normalise($raw['class'])])) {
            $available = implode(', ', array_keys($classMap));
            $e[] = "Semester '{$raw['class']}' not found. Available: {$available}.";
        }

        // ── Enrollment — Section ──────────────────────────────────────────
        if (empty($raw['section'])) {
            $e[] = 'Section is required (column X).';
        } elseif (! isset($sectionMap[$this->normalise($raw['section'])])) {
            $available = implode(', ', array_keys($sectionMap));
            $e[] = "Section '{$raw['section']}' not found. Available: {$available}.";
        }

        // ── Enrollment status ─────────────────────────────────────────────
        if (empty($raw['enrollment_status'])) {
            $e[] = 'Enrollment Status is required (column AC).';
        } elseif (! in_array($raw['enrollment_status'], ['active', 'promoted', 'transferred', 'completed'])) {
            $e[] = "Enrollment Status '{$raw['enrollment_status']}' is invalid. Use: active, promoted, transferred, completed.";
        }

        // ── Student type ──────────────────────────────────────────────────
        if (! empty($raw['student_type']) && ! in_array($raw['student_type'], ['regular', 'lateral'])) {
            $e[] = "Student Type '{$raw['student_type']}' must be 'regular' or 'lateral'.";
        }

        // ── Residence type ────────────────────────────────────────────────
        if (! empty($raw['residence_type']) && ! in_array($raw['residence_type'], ['day_scholar', 'hosteller'])) {
            $e[] = "Residence Type '{$raw['residence_type']}' must be 'day_scholar' or 'hosteller'.";
        }

        // ── Admission date ────────────────────────────────────────────────
        if (! empty($raw['dateof_admission']) && ! $this->isValidDate($raw['dateof_admission'])) {
            $e[] = "Admission Date '{$raw['dateof_admission']}' is not valid. Use YYYY-MM-DD.";
        }

        // ── Date of leaving ───────────────────────────────────────────────
        if (! empty($raw['date_of_leaving'])) {
            if (! $this->isValidDate($raw['date_of_leaving'])) {
                $e[] = "Leaving Date '{$raw['date_of_leaving']}' is not valid. Use YYYY-MM-DD.";
            } elseif (
                ! empty($raw['dateof_admission']) && $this->isValidDate($raw['dateof_admission']) &&
                Carbon::parse($raw['date_of_leaving'])->lt(Carbon::parse($raw['dateof_admission']))
            ) {
                $e[] = 'Leaving Date cannot be before Admission Date.';
            }
        }

        // ── Account type ──────────────────────────────────────────────────
        if (! empty($raw['account_type']) && ! in_array($raw['account_type'], ['savings', 'current'])) {
            $e[] = "Account Type '{$raw['account_type']}' must be 'savings' or 'current'.";
        }

        // ── IFSC ──────────────────────────────────────────────────────────
        if (! empty($raw['ifsc_code']) && strlen($raw['ifsc_code']) > 11) {
            $e[] = 'IFSC Code must be ≤ 11 characters.';
        }

        return $e;
    }

   private function createStudent(array $raw, int $tenantId, array $courseMap, array $streamMap): Student
{
    // ── Resolve course & stream objects for their codes ───────────────
    $courseId = $courseMap[$this->normalise($raw['course'])] ?? null;
    $streamId = $streamMap[$this->normalise($raw['stream'])] ?? null;

    $course = $courseId ? Course::find($courseId) : null;
    $stream = $streamId ? Stream::find($streamId) : null;

    $courseCode = $course?->code ?? 'NA';
    $streamCode = $stream?->code ?? 'NA';

    $studentType = $raw['student_type'] ?: 'regular';
    $typeCode    = ($studentType === 'lateral') ? 'L' : 'F';
    $year        = date('Y');

    $prefix = "SERC/{$courseCode}/{$streamCode}/{$year}/{$typeCode}/";

    $last = Student::where('tenant_id', $tenantId)
        ->where('admission_number', 'like', $prefix . '%')
        ->orderBy('id', 'desc')
        ->first();

    $next = 1;
    if ($last) {
        $parts = explode('/', $last->admission_number);
        $next  = (int) end($parts) + 1;
    }
    $admissionNumber = $prefix . str_pad($next, 6, '0', STR_PAD_LEFT);

    // ── Build student ─────────────────────────────────────────────────
    $dobPassword = ! empty($raw['dob'])
        ? Carbon::parse($raw['dob'])->format('dmY')
        : Str::random(8);

    $student = new Student;
    $student->tenant_id               = $tenantId;
    $student->user_id                 = auth()->id();
    $student->admission_number        = $admissionNumber;
    $student->username                = $admissionNumber;   // matches StudentController
    $student->password                = Hash::make($dobPassword);
    $student->first_name              = trim($raw['first_name']);
    $student->last_name               = trim($raw['last_name']);
    $student->gender                  = $raw['gender']       ?: null;
    $student->dob                     = ! empty($raw['dob']) ? $this->parseDate($raw['dob']) : null;
    $student->blood_group             = ! empty($raw['blood_group']) ? strtoupper(trim($raw['blood_group'])) : null;
    $student->religion                = $raw['religion']     ?: null;
    $student->nationality             = $raw['nationality']  ?: null;
    $student->aadhaar                 = $raw['aadhaar']      ?: null;
    $student->email                   = $raw['email']        ?: null;
    $student->category                = $raw['category']     ?: null;
    $student->sub_caste               = $raw['sub_caste']    ?: null;
    $student->physically_handicapped  = $raw['physically_handicapped'] ?: null;
    $student->handicap_details        = ($raw['physically_handicapped'] === 'yes') ? ($raw['handicap_details'] ?: null) : null;
    $student->emergency_contact_name  = $raw['emergency_contact_name']  ?: null;
    $student->emergency_contact_phone = $raw['emergency_contact_phone'] ?: null;
    $student->medical_conditions      = $raw['medical_conditions']      ?: null;
    $student->allergies               = $raw['allergies']               ?: null;
    $student->status                  = $raw['student_status'];
    $student->roll_number             = $raw['roll_number']             ?: null;
    $student->account_holder_name     = $raw['account_holder_name']     ?: null;
    $student->bank_name               = $raw['bank_name']               ?: null;
    $student->branch_name             = $raw['branch_name']             ?: null;
    $student->account_type            = $raw['account_type']            ?: null;
    $student->account_number          = $raw['account_number']          ?: null;
    $student->ifsc_code               = $raw['ifsc_code']               ?: null;
    $student->save();

    return $student;
}
    private function createEnrollment(
        array $raw, Student $student, int $tenantId,
        $ayMap, $courseMap, $streamMap, $classMap, $sectionMap
    ): void {
        $enrollment = new StudentClassEnrollment;
        $enrollment->tenant_id        = $tenantId;
        $enrollment->student_id       = $student->id;

        // Resolve names → IDs using normalised keys
        $courseId = $courseMap[$this->normalise($raw['course'])];
        $streamId = $streamMap[$this->normalise($raw['stream'])]  ?? null;
        $classId  = $classMap[$this->normalise($raw['class'])];

        $enrollment->academic_year_id = $ayMap[$this->normalise($raw['academic_year'])];
        $enrollment->course_id        = $courseId;
        $enrollment->stream_id        = $streamId;
        $enrollment->class_id         = $classId;
        $enrollment->section_id       = $sectionMap[$this->normalise($raw['section'])] ?? null;

        // Resolve ClassStreamMapping
        $courseStream = CourseStream::where('tenant_id', $tenantId)
            ->where('course_id', $courseId)
            ->where('stream_id', $streamId)
            ->first();

        if ($courseStream) {
            $mapping = ClassStreamMapping::where('tenant_id', $tenantId)
                ->where('course_stream_id', $courseStream->id)
                ->where('class_id', $classId)
                ->first();
            
            if ($mapping) {
                $enrollment->class_stream_id = $mapping->id;
            }
        }

        $enrollment->student_type     = $raw['student_type']    ?: 'regular';
        $enrollment->residence_type   = $raw['residence_type']  ?: null;
        $enrollment->dateof_admission = ! empty($raw['dateof_admission']) ? $this->parseDate($raw['dateof_admission']) : null;
        $enrollment->date_of_leaving  = ! empty($raw['date_of_leaving'])  ? $this->parseDate($raw['date_of_leaving'])  : null;
        $enrollment->status           = $raw['enrollment_status'];
        $enrollment->save();
    }

    // ─────────────────────────────────────────────────────────────
    // DATE UTILITIES
    // ─────────────────────────────────────────────────────────────

    private function isValidDate(string $date): bool
    {
        if ($date === '') return false;
        try {
            Carbon::parse($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function parseDate(string $date): ?string
    {
        if ($date === '') return null;
        try {
            // Excel serial number
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $date)->format('Y-m-d');
            }
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
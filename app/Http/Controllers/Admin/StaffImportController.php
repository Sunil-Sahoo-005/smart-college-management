<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StaffImportController extends Controller
{
    // ─────────────────────────────────────────────
    // SHOW IMPORT PAGE
    // ─────────────────────────────────────────────
    public function showImport()
    {
        $designations = Designation::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();
        $departments  = Department::where('tenant_id', auth()->user()->tenant_id)->orderBy('name')->get();

        return view('Academics.staff.import', compact('designations', 'departments'));
    }

    // ─────────────────────────────────────────────
    // DOWNLOAD TEMPLATE (dynamic dropdowns)
    // ─────────────────────────────────────────────
    public function downloadTemplate()
    {
        $tenantId     = auth()->user()->tenant_id;
        $designations = Designation::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();
        $departments  = Department::where('tenant_id', $tenantId)->orderBy('name')->pluck('name')->toArray();

        $templatePath = public_path('templates/staff_import_template.xlsx');

        // ── Load the base template ──────────────────────────────────────────
        $spreadsheet  = IOFactory::load($templatePath);
        $lookupSheet  = $spreadsheet->getSheetByName('_Lookups');
        $dataSheet    = $spreadsheet->getSheetByName('Staff Import');

        // ── Overwrite designations in _Lookups column A ─────────────────────
        for ($r = 2; $r <= 100; $r++) {
            $lookupSheet->getCell('A' . $r)->setValue('');
        }
        foreach ($designations as $i => $d) {
            $lookupSheet->getCell('A' . ($i + 2))->setValue($d);
        }

        // ── Overwrite departments in _Lookups column B ──────────────────────
        for ($r = 2; $r <= 100; $r++) {
            $lookupSheet->getCell('B' . $r)->setValue('');
        }
        foreach ($departments as $i => $d) {
            $lookupSheet->getCell('B' . ($i + 2))->setValue($d);
        }

        // ── Update data-validations ──────────────────────────────────────────
        $desEnd = max(count($designations), 1) + 1;
        $depEnd = max(count($departments),  1) + 1;

        foreach ($dataSheet->getDataValidationCollection() as $dv) {
            if (str_contains((string)$dv->getFormula1(), '_Lookups!$A$')) {
                $dv->setFormula1("_Lookups!\$A\$2:\$A\${$desEnd}");
            }
            if (str_contains((string)$dv->getFormula1(), '_Lookups!$B$')) {
                $dv->setFormula1("_Lookups!\$B\$2:\$B\${$depEnd}");
            }
        }

        // ── Stream to browser ───────────────────────────────────────────────
        $writer   = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'staff_import_template_' . date('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // ─────────────────────────────────────────────
    // PROCESS IMPORT
    // ─────────────────────────────────────────────
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
        $sheet       = $spreadsheet->getSheetByName('Staff Import')
                    ?? $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestDataRow();

        if ($highestRow < 3) {
            return back()->with('error', 'The uploaded file has no data rows. Please fill in staff data from row 3 onwards.');
        }

        // ── Lookup maps: name → id ───────────────────────────────────────────
        $designationMap = Designation::where('tenant_id', $tenantId)
            ->get()->mapWithKeys(fn($d) => [strtolower(trim($d->name)) => $d->id]);

        $departmentMap = Department::where('tenant_id', $tenantId)
            ->get()->mapWithKeys(fn($d) => [strtolower(trim($d->name)) => $d->id]);

        // ── Track results ────────────────────────────────────────────────────
        $errors   = [];
        $imported = 0;
        $skipped  = 0;

        $seenEmails = Staff::where('tenant_id', $tenantId)->pluck('email')->filter()->map('strtolower')->flip()->toArray();
        $seenPhones = Staff::where('tenant_id', $tenantId)->pluck('phone')->filter()->flip()->toArray();
        $fileEmails = [];
        $filePhones = [];

        // ════════════════════════════════════════════════════════════════════
        // COLUMN MAP (Excel template must follow this exact order)
        // ════════════════════════════════════════════════════════════════════
        // A  - first_name *
        // B  - last_name *
        // C  - email
        // D  - phone *
        // E  - whatsapp
        // F  - gender
        // G  - dob
        // H  - blood_group
        // I  - marital_status
        // J  - designation *
        // K  - department *
        // L  - employee_id
        // M  - bput_id
        // N  - employment_type
        // O  - joining_date *
        // P  - leaving_date
        // Q  - status *
        // R  - experience
        // S  - qualification
        // T  - religion
        // U  - nationality
        // V  - category
        // W  - pan
        // X  - aadhaar
        // Y  - pf_number
        // Z  - permanent_address
        // AA - current_address
        // AB - bank_account_name
        // AC - bank_account_number
        // AD - ifsc
        // AE - bank_name
        // AF - branch_name
        // AG - account_type
        // ════════════════════════════════════════════════════════════════════

        for ($row = 3; $row <= $highestRow; $row++) {
            $raw = [
                // ── Personal Info ────────────────────────────────────────────
                'first_name'          => trim((string)$sheet->getCell("A{$row}")->getValue()),
                'last_name'           => trim((string)$sheet->getCell("B{$row}")->getValue()),
                'email'               => trim(strtolower((string)$sheet->getCell("C{$row}")->getValue())),
                'phone'               => trim((string)$sheet->getCell("D{$row}")->getValue()),
                'whatsapp'            => trim((string)$sheet->getCell("E{$row}")->getValue()),
                'gender'              => trim(strtolower((string)$sheet->getCell("F{$row}")->getValue())),
                'dob'                 => trim((string)$sheet->getCell("G{$row}")->getValue()),
                'blood_group'         => trim((string)$sheet->getCell("H{$row}")->getValue()),
                'marital_status'      => trim(strtolower((string)$sheet->getCell("I{$row}")->getValue())),

                // ── Professional Info ─────────────────────────────────────────
                'designation_name'    => trim((string)$sheet->getCell("J{$row}")->getValue()),
                'department_name'     => trim((string)$sheet->getCell("K{$row}")->getValue()),
                'employee_id'         => trim((string)$sheet->getCell("L{$row}")->getValue()),
                'bput_id'             => trim((string)$sheet->getCell("M{$row}")->getValue()),
                'employment_type'     => trim((string)$sheet->getCell("N{$row}")->getValue()),
                'joining_date'        => trim((string)$sheet->getCell("O{$row}")->getValue()),
                'leaving_date'        => trim((string)$sheet->getCell("P{$row}")->getValue()),
                'status'              => trim(strtolower((string)$sheet->getCell("Q{$row}")->getValue())),
                'experience'          => trim((string)$sheet->getCell("R{$row}")->getValue()),
                'qualification'       => trim((string)$sheet->getCell("S{$row}")->getValue()),
                'religion'            => trim((string)$sheet->getCell("T{$row}")->getValue()),
                'nationality'         => trim((string)$sheet->getCell("U{$row}")->getValue()),
                'category'               => trim((string)$sheet->getCell("V{$row}")->getValue()),
                'pan'                 => trim(strtoupper((string)$sheet->getCell("W{$row}")->getValue())),
                'aadhaar'             => trim((string)$sheet->getCell("X{$row}")->getValue()),
                'pf_number'           => trim((string)$sheet->getCell("Y{$row}")->getValue()),
                'permanent_address'   => trim((string)$sheet->getCell("Z{$row}")->getValue()),
                'current_address'     => trim((string)$sheet->getCell("AA{$row}")->getValue()),

                // ── Bank Info ─────────────────────────────────────────────────
                'bank_account_name'   => trim((string)$sheet->getCell("AB{$row}")->getValue()),
                'bank_account_number' => trim((string)$sheet->getCell("AC{$row}")->getValue()),
                'ifsc'                => trim(strtoupper((string)$sheet->getCell("AD{$row}")->getValue())),
                'bank_name'           => trim((string)$sheet->getCell("AE{$row}")->getValue()),
                'branch_name'         => trim((string)$sheet->getCell("AF{$row}")->getValue()),
                'account_type'        => trim(strtolower((string)$sheet->getCell("AG{$row}")->getValue())),
            ];

            // ── Experience से "Years" / "year" text हटाओ ─────────────────────
            $raw['experience'] = trim(preg_replace('/[^0-9.]/i', '', $raw['experience']));

            // Skip fully empty rows
            if ($this->isEmptyRow($raw)) {
                $skipped++;
                continue;
            }

            $rowErrors = $this->validateRow(
                $raw, $row,
                $designationMap, $departmentMap,
                $seenEmails, $seenPhones,
                $fileEmails, $filePhones
            );

            if (!empty($rowErrors)) {
                $errors[] = ['row' => $row, 'data' => $raw, 'messages' => $rowErrors];
                continue;
            }

            // ── Mark as seen within this file ────────────────────────────────
            if ($raw['email']) $fileEmails[$raw['email']] = $row;
            if ($raw['phone']) $filePhones[$raw['phone']] = $row;

            // ── Persist ──────────────────────────────────────────────────────
            $staff = new Staff;
            $staff->tenant_id           = $tenantId;

            // Personal Info
            $staff->first_name          = $raw['first_name'];
            $staff->last_name           = $raw['last_name'];
            $staff->email               = $raw['email']           ?: null;
            $staff->phone               = $raw['phone'];
            $staff->whatsapp            = $raw['whatsapp']        ?: null;
            $staff->gender              = $raw['gender']          ?: null;
            $staff->dob                 = $raw['dob']             ? $this->parseDate($raw['dob']) : null;
            $staff->blood_group         = $raw['blood_group']     ?: null;
            $staff->marital_status      = $raw['marital_status']  ? ucfirst($raw['marital_status'])  : null;
            $staff->permanent_address   = $raw['permanent_address'] ?: null;

            // Professional Info
            $staff->designation_id      = $designationMap[strtolower($raw['designation_name'])];
            $staff->department_id       = $departmentMap[strtolower($raw['department_name'])];
            $staff->employee_id         = $raw['employee_id']     ?: null;
            $staff->bput_id             = $raw['bput_id']         ?: null;
            $staff->employment_type     = $raw['employment_type'] ? ucwords($raw['employment_type']) : null;
            $staff->joining_date        = $this->parseDate($raw['joining_date']);
            $staff->resignation_date    = $raw['leaving_date']    ? $this->parseDate($raw['leaving_date']) : null;
            $staff->status              = str_replace(' ', '_', $raw['status']);
            $staff->experience          = is_numeric($raw['experience']) ? (float)$raw['experience'] : null;
            $staff->qualification       = $raw['qualification']   ?: null;
            $staff->religion            = $raw['religion']        ?: null;
            $staff->nationality         = $raw['nationality']     ?: null;
            $staff->category               = $raw['category']           ?: null;
            $staff->pan                 = $raw['pan']             ?: null;
            $staff->aadhaar             = $raw['aadhaar']         ?: null;
            $staff->pf_number           = $raw['pf_number']       ?: null;
            $staff->current_address     = $raw['current_address'] ?: null;

            // Bank Info
            $staff->bank_account_name   = $raw['bank_account_name']   ?: null;
            $staff->bank_account_number = $raw['bank_account_number'] ?: null;
            $staff->ifsc                = $raw['ifsc']            ?: null;
            $staff->bank_name           = $raw['bank_name']       ?: null;
            $staff->branch_name         = $raw['branch_name']     ?: null;
            $staff->account_type        = $raw['account_type']    ? ucfirst($raw['account_type'])    : null;

            try {
                $staff->save();
                $imported++;

                if ($raw['email']) $seenEmails[$raw['email']] = true;
                if ($raw['phone']) $seenPhones[$raw['phone']] = true;

            } catch (\Exception $e) {
                $errors[] = [
                    'row'      => $row,
                    'data'     => $raw,
                    'messages' => ['Database error: ' . $e->getMessage()],
                ];
            }
        }

        // ── Build result session data ────────────────────────────────────────
        $summary = [
            'imported' => $imported,
            'errors'   => count($errors),
            'skipped'  => $skipped,
        ];

        if (!empty($errors)) {
            session()->flash('import_errors', $errors);
        }

        session()->flash('import_summary', $summary);

        $message = "Import complete — {$imported} staff added.";
        if (count($errors)) $message .= " {$summary['errors']} row(s) had errors (see below).";
        if ($skipped)       $message .= " {$skipped} blank row(s) skipped.";

        return redirect()->route('admin.staff.import.show')
            ->with($imported > 0 ? 'success' : 'warning', $message);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    private function isEmptyRow(array $raw): bool
    {
        return collect($raw)->every(fn($v) => $v === '' || $v === null);
    }

    private function validateRow(
        array $raw,
        int $row,
        $designationMap,
        $departmentMap,
        array &$seenEmails,
        array &$seenPhones,
        array &$fileEmails,
        array &$filePhones
    ): array {
        $errs = [];

        // ── Required: First Name ─────────────────────────────────────────────
        if (empty($raw['first_name']))           $errs[] = 'First Name is required.';
        elseif (strlen($raw['first_name']) > 50) $errs[] = 'First Name must be ≤ 50 characters.';

        // ── Required: Last Name ──────────────────────────────────────────────
        if (empty($raw['last_name']))            $errs[] = 'Last Name is required.';
        elseif (strlen($raw['last_name']) > 50)  $errs[] = 'Last Name must be ≤ 50 characters.';

        // ── Required: Phone ──────────────────────────────────────────────────
        if (empty($raw['phone'])) {
            $errs[] = 'Phone is required.';
        } else {
            $phone = preg_replace('/\D/', '', $raw['phone']);
            if (strlen($phone) < 10 || strlen($phone) > 15) {
                $errs[] = "Phone must be 10–15 digits (got '{$raw['phone']}').";
            } elseif (isset($seenPhones[$raw['phone']])) {
                $errs[] = "Phone '{$raw['phone']}' already exists in the system.";
            } elseif (isset($filePhones[$raw['phone']])) {
                $errs[] = "Phone '{$raw['phone']}' is duplicated within this file (first seen on row {$filePhones[$raw['phone']]}).";
            }
        }

        // ── Optional: WhatsApp ───────────────────────────────────────────────
        if (!empty($raw['whatsapp'])) {
            $wa = preg_replace('/\D/', '', $raw['whatsapp']);
            if (strlen($wa) < 10 || strlen($wa) > 15) {
                $errs[] = "WhatsApp number must be 10–15 digits (got '{$raw['whatsapp']}').";
            }
        }

        // ── Optional: Email ──────────────────────────────────────────────────
        if (!empty($raw['email'])) {
            if (!filter_var($raw['email'], FILTER_VALIDATE_EMAIL)) {
                $errs[] = "Email '{$raw['email']}' is not a valid email address.";
            } elseif (isset($seenEmails[$raw['email']])) {
                $errs[] = "Email '{$raw['email']}' already exists in the system.";
            } elseif (isset($fileEmails[$raw['email']])) {
                $errs[] = "Email '{$raw['email']}' is duplicated within this file (first seen on row {$fileEmails[$raw['email']]}).";
            }
        }

        // ── Optional: Gender ─────────────────────────────────────────────────
        if (!empty($raw['gender']) && !in_array($raw['gender'], ['male', 'female', 'other'])) {
            $errs[] = "Gender must be male, female, or other (got '{$raw['gender']}').";
        }

        // ── Optional: DOB ────────────────────────────────────────────────────
        if (!empty($raw['dob']) && !$this->isValidDate($raw['dob'])) {
            $errs[] = "DOB '{$raw['dob']}' is not a valid date (use YYYY-MM-DD).";
        }

        // ── Optional: Blood Group ────────────────────────────────────────────
        $validBG = ['a+', 'a-', 'b+', 'b-', 'ab+', 'ab-', 'o+', 'o-'];
        if (!empty($raw['blood_group']) && !in_array(strtolower($raw['blood_group']), $validBG)) {
            $errs[] = "Blood Group '{$raw['blood_group']}' is invalid. Valid: A+, A-, B+, B-, AB+, AB-, O+, O-";
        }

        // ── Optional: Marital Status ─────────────────────────────────────────
        $validMarital = ['single', 'married', 'divorced', 'widowed'];
        if (!empty($raw['marital_status']) && !in_array($raw['marital_status'], $validMarital)) {
            $errs[] = "Marital Status must be single, married, divorced, or widowed (got '{$raw['marital_status']}').";
        }

        // ── Required: Designation ────────────────────────────────────────────
        if (empty($raw['designation_name'])) {
            $errs[] = 'Designation is required.';
        } elseif (!isset($designationMap[strtolower($raw['designation_name'])])) {
            $errs[] = "Designation '{$raw['designation_name']}' not found in the system. Create it first.";
        }

        // ── Required: Department ─────────────────────────────────────────────
        if (empty($raw['department_name'])) {
            $errs[] = 'Department is required.';
        } elseif (!isset($departmentMap[strtolower($raw['department_name'])])) {
            $errs[] = "Department '{$raw['department_name']}' not found in the system. Create it first.";
        }

        // ── Required: Joining Date ───────────────────────────────────────────
        if (empty($raw['joining_date'])) {
            $errs[] = 'Joining Date is required.';
        } elseif (!$this->isValidDate($raw['joining_date'])) {
            $errs[] = "Joining Date '{$raw['joining_date']}' is not valid (use YYYY-MM-DD).";
        }

        // ── Optional: Leaving Date ───────────────────────────────────────────
        if (!empty($raw['leaving_date'])) {
            if (!$this->isValidDate($raw['leaving_date'])) {
                $errs[] = "Leaving Date '{$raw['leaving_date']}' is not valid (use YYYY-MM-DD).";
            } elseif (!empty($raw['joining_date']) && $this->isValidDate($raw['joining_date'])) {
                if (Carbon::parse($raw['leaving_date'])->lt(Carbon::parse($raw['joining_date']))) {
                    $errs[] = 'Leaving Date cannot be before Joining Date.';
                }
            }
        }

        // ── Required: Status ─────────────────────────────────────────────────
        if (empty($raw['status'])) {
            $errs[] = 'Status is required.';
        } elseif (!in_array($raw['status'], ['active', 'resigned', 'retired', 'on_leave', 'on leave'])) {
            $errs[] = "Status must be 'active', 'resigned', 'retired', or 'on_leave' (got '{$raw['status']}').";
        }

        // ── Optional: Experience ─────────────────────────────────────────────
        if (!empty($raw['experience']) && !is_numeric($raw['experience'])) {
            $errs[] = "Experience must be a number (e.g. 5 or 2.5) (got '{$raw['experience']}').";
        }

        // ── Optional: PAN ────────────────────────────────────────────────────
        if (!empty($raw['pan']) && !preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]$/', $raw['pan'])) {
            $errs[] = "PAN '{$raw['pan']}' is invalid. Format: ABCDE1234F";
        }

        // ── Optional: Aadhaar ────────────────────────────────────────────────
        if (!empty($raw['aadhaar'])) {
            $aadhaar = preg_replace('/\D/', '', $raw['aadhaar']);
            if (strlen($aadhaar) !== 12) {
                $errs[] = "Aadhaar must be exactly 12 digits (got '{$raw['aadhaar']}').";
            }
        }

        // ── Optional: IFSC ───────────────────────────────────────────────────
        if (!empty($raw['ifsc']) && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $raw['ifsc'])) {
            $errs[] = "IFSC '{$raw['ifsc']}' is invalid. Format: SBIN0001234";
        }

        // ── Optional: Account Type ───────────────────────────────────────────
        $validAccountTypes = ['savings', 'current'];
        if (!empty($raw['account_type']) && !in_array($raw['account_type'], $validAccountTypes)) {
            $errs[] = "Account Type must be savings or current (got '{$raw['account_type']}').";
        }

        return $errs;
    }

    private function isValidDate(string $date): bool
    {
        try {
            if (is_numeric($date)) {
                Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d'));
                return true;
            }
            Carbon::createFromFormat('Y-m-d', $date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function parseDate(string $date): ?string
    {
        try {
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$date)->format('Y-m-d');
            }
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
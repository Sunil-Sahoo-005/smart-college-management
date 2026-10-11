<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\StudentClassEnrollment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;

class StudentsImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    /**
     * Row 1 = headers (WithHeadingRow reads this).
     * Row 2 = hint row  → skip (first_name will be "⚠ REQUIRED — Required. First name")
     * Row 3 = example   → skip (first_name will be "Rahul")
     * Row 4+ = real data
     *
     * We skip rows 2 & 3 by checking for the known hint/example sentinel values.
     */
    public function model(array $row)
    {
        $firstName = trim($row['first_name'] ?? '');

        // Skip blank rows
        if ($firstName === '') {
            return null;
            dd('skipped blank row');
        }

        // Skip the hint row (row 2) — value starts with "⚠"
        if (str_starts_with($firstName, '⚠')) {
            return null;
            dd('skipped hint row');
        }

        // Skip the example row (row 3) — sentinel value we know
        // if (strtolower($firstName) === 'rahul') {
        //     // dd('skipped example row');
        //     // More robust: skip if last_name is also the known example value
        //     if (strtolower(trim($row['last_name'] ?? '')) === 'sharma'
        //         && trim($row['aadhaar'] ?? '') === '123456789012') {
        //         return null;
        //     }
        // }

        $tenantId = Auth::user()->tenant_id;

        // ── Generate admission number ─────────────────────────────────────
        $tenant = Auth::user()->tenant;
        $prefix = strtoupper(substr(preg_replace('/\s+/', '', $tenant->name), 0, 2));
        $year   = date('Y');

        $last = Student::where('tenant_id', $tenantId)
            ->where('admission_number', 'like', "{$prefix}/{$year}/%")
            ->orderBy('id', 'desc')
            ->first();

        $next            = $last ? (intval(explode('/', $last->admission_number)[2]) + 1) : 1;
        $admissionNumber = "{$prefix}/{$year}/" . str_pad($next, 6, '0', STR_PAD_LEFT);

        // ── DOB & default password ────────────────────────────────────────
        $dob         = !empty($row['dob']) ? Carbon::parse($row['dob']) : null;
        $dobPassword = $dob ? $dob->format('dmY') : 'Pass@1234';

        // ── Upsert student (avoid duplicates on aadhaar) ──────────────────
        $aadhaar = !empty($row['aadhaar']) ? trim($row['aadhaar']) : null;

        $student = Student::firstOrCreate(
            array_filter([
                'tenant_id' => $tenantId,
                'aadhaar'   => $aadhaar,   // match key — null is ignored by array_filter
            ]),
            [
                'admission_number'        => $admissionNumber,
                'tenant_id'               => $tenantId,
                'user_id'                 => Auth::id(),
                'first_name'              => $firstName,
                'last_name'               => trim($row['last_name'] ?? ''),
                'username'                => $firstName,
                'password'                => Hash::make($dobPassword),
                'gender'                  => $row['gender']       ?? null,
                'dob'                     => $dob,
                'blood_group'             => $row['blood_group']  ?? null,
                'religion'                => $row['religion']     ?? null,
                'nationality'             => $row['nationality']  ?? null,
                'roll_number'             => $row['roll_number']  ?? null,
                'status'                  => $row['status']       ?? 'active',
                'category'                => $row['category']     ?? null,
                'emergency_contact_name'  => $row['emergency_contact_name']  ?? null,
                'emergency_contact_phone' => $row['emergency_contact_phone'] ?? null,
                'medical_conditions'      => $row['medical_conditions']      ?? null,
                'allergies'               => $row['allergies']               ?? null,
            ]
        );

        // ── Enrollment ────────────────────────────────────────────────────
        $academicYearId = $row['academic_year_id'] ?? null;
        $classId        = $row['class_id']         ?? null;

        if ($academicYearId && $classId) {
            $admissionDate = !empty($row['dateof_admission'])
                ? Carbon::parse($row['dateof_admission'])
                : now();

            StudentClassEnrollment::firstOrCreate(
                [
                    'tenant_id'        => $tenantId,
                    'student_id'       => $student->id,
                    'academic_year_id' => $academicYearId,
                ],
                [
                    'class_id'        => $classId,
                    'section_id'      => $row['section_id']   ?? null,
                    'stream_id'       => $row['stream_id']    ?? null,
                    'student_type'    => $row['student_type'] ?? 'regular',
                    'residence_type'  => $row['residence_type'] ?? 'day_scholar',
                    'dateof_admission'=> $admissionDate,
                    'status'          => 'active',
                ]
            );
        }

        // Return null because we used firstOrCreate manually (not letting
        // Maatwebsite insert for us), so no duplicate insert happens.
        return null;
    }
}
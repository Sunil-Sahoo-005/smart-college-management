<?php

namespace App\Observers;

use App\Models\StudentClassEnrollment;
use App\Models\LibraryCards;

class StudentClassEnrollmentObserver
{
    public $afterCommit = true;

    public function created(StudentClassEnrollment $studentClassEnrollment): void
    {
        $alreadyHasCard = LibraryCards::where('tenant_id', $studentClassEnrollment->tenant_id)
            ->where('student_id', $studentClassEnrollment->student_id)
            ->exists();

        if (!$alreadyHasCard) {
            $cardNumber = 'LIB'
                . '-' . $studentClassEnrollment->tenant_id
                . '-' . $studentClassEnrollment->academic_year_id
                . '-' . str_pad($studentClassEnrollment->student_id, 5, '0', STR_PAD_LEFT);

            LibraryCards::create([
                'tenant_id'   => $studentClassEnrollment->tenant_id,
                'student_id'  => $studentClassEnrollment->student_id,
                'card_number' => $cardNumber,
                'issue_date'  => now()->toDateString(),
                'expiry_date' => now()->addYears(4)->toDateString(),
                'status'      => 'active',
            ]);
        }
    }

    public function updated(StudentClassEnrollment $studentClassEnrollment): void {}
    public function deleted(StudentClassEnrollment $studentClassEnrollment): void {}
    public function restored(StudentClassEnrollment $studentClassEnrollment): void {}
    public function forceDeleted(StudentClassEnrollment $studentClassEnrollment): void {}
}
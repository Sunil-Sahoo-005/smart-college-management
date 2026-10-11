<?php

namespace App\Traits;

use App\Models\FeeInstallment;
use App\Models\StudentFeeStructure;
use Illuminate\Support\Carbon;

trait HandlesFeeInstallments
{
    /**
     * Generates fee installments for a specific student fee structure assignment.
     * Fixes date comparison and academic year month logic.
     */
    public function generateInstallments(StudentFeeStructure $plan): int
    {
        \Log::info("Starting generation for Plan ID: {$plan->id}, Student: {$plan->student_id}, Structure: {$plan->fee_structure_id}");

        $plan->load('feeStructure.items', 'student');
        
        if (!$plan->feeStructure) {
            \Log::warning("FeeStructure missing for plan {$plan->id}");
            return 0;
        }

        $items = $plan->feeStructure->items;
        \Log::info("Found " . $items->count() . " items to process.");

        // Normalize dates to ensure they are Carbon objects
        $startDate = Carbon::parse($plan->start_date);
        $endDate = Carbon::parse($plan->end_date);

        $installmentsToCreate = [];
        
        foreach ($items as $index => $item) {
            // Calculate due date based on month. If month null, use end of session month.
            $month = $item->due_month ?? $endDate->month;
            $year = $startDate->year;
            
            // If month is logically before the session start month, it likely belongs to next calendar year
            if ($month < $startDate->month && $endDate->year > $startDate->year) {
                $year++;
            }

            $due = Carbon::create($year, $month, 1)->endOfMonth()->startOfDay();
            $dateString = $due->toDateString();

            \Log::info("Item {$index}: Category {$item->fee_category_id}, Month {$month}, Target Date {$dateString}");

            $key = $item->fee_category_id . '_' . $dateString;
            
            if (!isset($installmentsToCreate[$key])) {
                $installmentsToCreate[$key] = [
                    'fee_category_id' => $item->fee_category_id,
                    'due_date'        => $dateString,
                    'amount'          => 0,
                ];
            }
            $installmentsToCreate[$key]['amount'] += (float)$item->amount;
        }

        $createdCount = 0;
        foreach ($installmentsToCreate as $key => $data) {
            // Include student_fee_structure_id in the check to prevent collision between structures
            $installment = FeeInstallment::firstOrCreate(
                [
                    'tenant_id'                => $plan->tenant_id,
                    'student_id'               => $plan->student_id,
                    'student_fee_structure_id' => $plan->id, // Critical link
                    'fee_category_id'          => $data['fee_category_id'],
                    'due_date'                 => $data['due_date'],
                ],
                [
                    'amount'                   => $data['amount'],
                    'status'                   => 'unpaid',
                ]
            );

            if ($installment->wasRecentlyCreated) {
                \Log::info("  Successfully created installment: {$key} with amount {$data['amount']}");
                $createdCount++;
            } else {
                \Log::info("  Installment {$key} already exists for this structure. Current amount: " . $installment->amount);
            }
        }

        $plan->update(['status' => 'generated']);
        \Log::info("Finished. Created {$createdCount} new installments.");

        return $createdCount;
    }
}

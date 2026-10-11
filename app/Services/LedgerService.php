<?php

namespace App\Services;

use App\Models\Ledger;
use App\Models\LedgerEntry;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * LedgerService
 *
 * Central place for all double-entry ledger recording.
 * Call one of the record*() helpers from any controller or observer.
 *
 * Every helper writes debit/credit rows together so source records
 * and ledger entries stay aligned.
 */
class LedgerService
{
    // ─────────────────────────────────────────────────────────────
    //  FEE PAYMENT  (cash/bank in, fee income credited)
    // ─────────────────────────────────────────────────────────────
    public function recordFeePayment(\App\Models\FeePayment $payment): void
    {
        $tenantId    = $payment->tenant_id;
        $description = "Fee receipt #{$payment->receipt_number}";

        // Debit side – the account money landed in
        $drLedgerName = match ($payment->payment_method) {
            'cash'          => 'Cash',
            'bank_transfer' => $payment->gateway_name ?? 'Bank',
            'online'        => 'Online Gateway',
            default         => 'Cash',
        };

        $this->post(
            tenantId:      $tenantId,
            drName:        $drLedgerName,
            drType:        'debit',
            crName:        'Fee Income',
            crType:        'credit',
            amount:        $payment->amount_paid,
            referenceType: \App\Models\FeePayment::class,
            referenceId:   $payment->id,
            description:   $description,
            entryDate:     $payment->payment_date,
        );
    }

    // ─────────────────────────────────────────────────────────────
    //  INCOME RECORD  (cash/bank in, income head credited)
    // ─────────────────────────────────────────────────────────────
    public function recordIncome(\App\Models\IncomeRecord $record, string $paymentMode = 'cash'): void
    {
        $tenantId    = $record->tenant_id;
        $headName    = $record->incomeHead->name;
        $description = "Income – {$headName}";

        $this->post(
            tenantId:      $tenantId,
            drName:        ucfirst($paymentMode),
            drType:        'debit',
            crName:        $headName,
            crType:        'credit',
            amount:        $record->amount,
            referenceType: \App\Models\IncomeRecord::class,
            referenceId:   $record->id,
            description:   $description,
            entryDate:     $record->date,
        );
    }

    // ─────────────────────────────────────────────────────────────
    //  EXPENSE RECORD  (expense head debited, cash/bank credited)
    // ─────────────────────────────────────────────────────────────
    public function recordExpense(\App\Models\ExpenseRecord $record, string $paymentMode = 'cash'): void
    {
        $tenantId    = $record->tenant_id;
        $headName    = $record->expenseHead->name;
        $description = $record->description ?? "Expense – {$headName}";

        $this->post(
            tenantId:      $tenantId,
            drName:        $headName,
            drType:        'debit',
            crName:        ucfirst($paymentMode),
            crType:        'debit',      // Cash is a debit-balance account; crediting it decreases it
            amount:        $record->amount,
            referenceType: \App\Models\ExpenseRecord::class,
            referenceId:   $record->id,
            description:   $description,
            entryDate:     $record->date,
        );
    }

    // ─────────────────────────────────────────────────────────────
    //  SALARY PAYMENT  (salary expense debited, payable / cash credited)
    // ─────────────────────────────────────────────────────────────
    public function recordSalary(\App\Models\SalaryRecord $record): void
    {
        $tenantId    = $record->tenant_id;
        $staffName   = $record->staff->name ?? "Staff #{$record->staff_id}";
        $description = "Salary – {$staffName} ({$record->month}/{$record->year})";

        $this->post(
            tenantId:      $tenantId,
            drName:        'Salary Expense',
            drType:        'debit',
            crName:        'Salary Payable',
            crType:        'credit',
            amount:        $record->net_pay,
            referenceType: \App\Models\SalaryRecord::class,
            referenceId:   $record->id,
            description:   $description,
            entryDate:     now()->toDateString(),
        );
    }

    public function repostFeePayment(\App\Models\FeePayment $payment): void
    {
        $this->forgetReference(\App\Models\FeePayment::class, $payment->id, $payment->tenant_id);
        $this->recordFeePayment($payment);
    }

    public function repostIncome(\App\Models\IncomeRecord $record, string $paymentMode = 'cash'): void
    {
        $this->forgetReference(\App\Models\IncomeRecord::class, $record->id, $record->tenant_id);
        $this->recordIncome($record, $paymentMode);
    }

    public function repostExpense(\App\Models\ExpenseRecord $record, string $paymentMode = 'cash'): void
    {
        $this->forgetReference(\App\Models\ExpenseRecord::class, $record->id, $record->tenant_id);
        $this->recordExpense($record, $paymentMode);
    }

    public function forgetReference(string $referenceType, int $referenceId, int $tenantId): void
    {
        LedgerEntry::where('tenant_id', $tenantId)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->delete();
    }

    // ─────────────────────────────────────────────────────────────
    //  REVERSAL  (flip all entries for a reference)
    // ─────────────────────────────────────────────────────────────
    public function reverse(string $referenceType, int $referenceId, int $tenantId): void
    {
        $original = LedgerEntry::where('tenant_id', $tenantId)
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->get();

        DB::transaction(function () use ($original, $tenantId, $referenceType, $referenceId) {
            foreach ($original as $entry) {
                LedgerEntry::create([
                    'tenant_id'      => $tenantId,
                    'ledger_id'      => $entry->ledger_id,
                    'amount'         => $entry->amount,
                    'type'           => $entry->type === 'debit' ? 'credit' : 'debit',
                    'reference_type' => $referenceType,
                    'reference_id'   => $referenceId,
                    'description'    => "Reversal of entry #{$entry->id}",
                    'entry_date'     => $entry->entry_date ?? now()->toDateString(),
                ]);
            }
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  LOW-LEVEL DOUBLE-ENTRY POST
    //  Creates exactly one debit and one credit entry.
    // ─────────────────────────────────────────────────────────────
    private function post(
        int    $tenantId,
        string $drName,
        string $drType,
        string $crName,
        string $crType,
        float  $amount,
        string $referenceType,
        int    $referenceId,
        string $description,
        CarbonInterface|string|null $entryDate = null,
    ): void {
        DB::transaction(function () use (
            $tenantId, $drName, $drType, $crName, $crType,
            $amount, $referenceType, $referenceId, $description, $entryDate
        ) {
            $alreadyPosted = LedgerEntry::where('tenant_id', $tenantId)
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->exists();

            if ($alreadyPosted) {
                return;
            }

            $postingDate = $entryDate
                ? Carbon::parse($entryDate)->toDateString()
                : now()->toDateString();

            $drLedger = $this->resolveLedger($tenantId, $drName, $drType);
            $crLedger = $this->resolveLedger($tenantId, $crName, $crType);

            LedgerEntry::create([
                'tenant_id'      => $tenantId,
                'ledger_id'      => $drLedger->id,
                'amount'         => $amount,
                'type'           => 'debit',
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
                'entry_date'     => $postingDate,
            ]);

            LedgerEntry::create([
                'tenant_id'      => $tenantId,
                'ledger_id'      => $crLedger->id,
                'amount'         => $amount,
                'type'           => 'credit',
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'description'    => $description,
                'entry_date'     => $postingDate,
            ]);
        });
    }

    private function resolveLedger(int $tenantId, string $name, string $balanceType): Ledger
    {
        return Ledger::firstOrCreate(
            ['tenant_id' => $tenantId, 'name' => $name],
            ['balance_type' => $balanceType]
        );
    }
}

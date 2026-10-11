<?php

namespace App\Exports;

use App\Models\ExpenseRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ExpenseRecordsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $records;

    public function __construct($records = null)
    {
        $this->records = $records ?? collect();
    }

    public function collection()
    {
        if ($this->records->isEmpty()) {
            return collect([['No records found']]);
        }

        return $this->records->map(function ($record) {
            return [
                $record->id,
                $record->date ? $record->date->format('d-m-Y') : '',
                $record->expenseHead->name ?? 'Unknown Head',
                $record->amount ? '₹' . number_format($record->amount, 2) : '₹0.00',
                $record->description ?? '',
                $record->created_at ? $record->created_at->format('d-m-Y H:i') : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Expense Head',
            'Amount',
            'Description',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545'] // Red for expenses
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        return [];
    }

    public function title(): string
    {
        return 'Expense Records';
    }
}

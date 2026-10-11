<?php

namespace App\Exports;

use App\Models\IncomeRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class IncomeRecordsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $records;

    public function __construct($records = null)
    {
        $this->records = $records ?? collect(); // ✅ Fix: Handle empty collection
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->records->isEmpty()) {
            return collect([['No records found']]);
        }

        return $this->records->map(function ($record) {
            return [
                $record->id,                                    // ID
                $record->date ? $record->date->format('d-m-Y') : '',  // Date (safe check)
                $record->incomeHead->name ?? 'Unknown Head',    // Head (safe check)
                $record->amount ? '₹' . number_format($record->amount, 2) : '₹0.00',  // Amount
                $record->description ?? '',                     // Description
                $record->created_at ? $record->created_at->format('d-m-Y H:i') : '',  // Created
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Income Head',
            'Amount',
            'Description',
            'Created At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Amount column right align
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        return [];
    }

    public function title(): string
    {
        return 'Income Records';
    }
}

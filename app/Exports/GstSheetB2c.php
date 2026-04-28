<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class GstSheetB2c implements
    FromCollection, WithHeadings, WithTitle,
    WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        private array  $data,
        private string $from,
        private string $to
    ) {}

    public function title(): string { return 'B2C Unregistered'; }

    public function headings(): array
    {
        return [
            ['B2C — Unregistered Customer Summary (State-wise)'],
            ['Period: ' . $this->from . ' to ' . $this->to],
            [],
            ['State', 'Tax Type', 'Invoices',
             'Taxable Value', 'CGST', 'SGST', 'IGST', 'Total Tax', 'Grand Total'],
        ];
    }

    public function collection(): Collection
    {
        $rows = $this->data['rows']->map(fn ($r) => [
            $r->customer_state ?: 'Not specified',
            $r->is_intra_state ? 'CGST + SGST' : 'IGST',
            $r->inv_count,
            $r->taxable, $r->cgst, $r->sgst, $r->igst,
            $r->total_tax, $r->grand,
        ]);

        $t = $this->data['totals'];
        $rows->push([]);
        $rows->push([
            'TOTAL', '', $t['invoices'],
            $t['taxable'], $t['cgst'], $t['sgst'], $t['igst'],
            $t['total_tax'], $t['grand'],
        ]);

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 14, 'C' => 10,
            'D' => 16, 'E' => 14, 'F' => 14, 'G' => 14,
            'H' => 14, 'I' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                foreach (['D', 'E', 'F', 'G', 'H', 'I'] as $col) {
                    $sheet->getStyle("{$col}5:{$col}{$lastRow}")
                          ->getNumberFormat()->setFormatCode('#,##0.00');
                }

                $sheet->getStyle("A{$lastRow}:I{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->freezePane('A5');
            },
        ];
    }
}

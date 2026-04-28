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

class GstSheetGstr1 implements
    FromCollection, WithHeadings, WithTitle,
    WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        private array  $data,
        private string $from,
        private string $to
    ) {}

    public function title(): string { return 'GSTR-1 Summary'; }

    public function headings(): array
    {
        return [
            ['GSTR-1 Summary Report'],
            ['Period: ' . $this->from . ' to ' . $this->to],
            [],
            ['Invoice #', 'Date', 'Customer', 'GSTIN', 'State', 'Type',
             'Taxable Value', 'CGST', 'SGST', 'IGST', 'Total Tax', 'Grand Total'],
        ];
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->data['b2b'] as $r) {
            $rows->push([
                $r['number'], $r['date'], $r['customer'], $r['gstin'] ?: '',
                $r['state'] ?: '', 'B2B',
                $r['taxable'], $r['cgst'], $r['sgst'], $r['igst'],
                $r['total_tax'], $r['grand'],
            ]);
        }

        foreach ($this->data['b2c'] as $r) {
            $rows->push([
                $r['number'], $r['date'], $r['customer'] ?: '', '',
                $r['state'] ?: '', 'B2C',
                $r['taxable'], $r['cgst'], $r['sgst'], $r['igst'],
                $r['total_tax'], $r['grand'],
            ]);
        }

        $t = $this->data['totals'];
        $rows->push([]);
        $rows->push([
            'TOTAL', '', '', '', '', '',
            $t['taxable'], $t['cgst'], $t['sgst'], $t['igst'],
            $t['total_tax'], $t['grand'],
        ]);

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16, 'B' => 14, 'C' => 28, 'D' => 20,
            'E' => 18, 'F' => 8,
            'G' => 16, 'H' => 14, 'I' => 14, 'J' => 14,
            'K' => 14, 'L' => 16,
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

                // Number format for currency columns G-L
                foreach (['G', 'H', 'I', 'J', 'K', 'L'] as $col) {
                    $sheet->getStyle("{$col}5:{$col}{$lastRow}")
                          ->getNumberFormat()->setFormatCode('#,##0.00');
                }

                // Total row styling
                $sheet->getStyle("A{$lastRow}:L{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Freeze header
                $sheet->freezePane('A5');
            },
        ];
    }
}

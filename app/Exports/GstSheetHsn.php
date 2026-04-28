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

class GstSheetHsn implements
    FromCollection, WithHeadings, WithTitle,
    WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        private array  $data,
        private string $from,
        private string $to
    ) {}

    public function title(): string { return 'HSN Summary'; }

    public function headings(): array
    {
        return [
            ['HSN / SAC Wise Summary'],
            ['Period: ' . $this->from . ' to ' . $this->to],
            [],
            ['HSN/SAC', 'Unit', 'GST Rate %', 'Total Qty',
             'Taxable Value', 'CGST', 'SGST', 'IGST', 'Total Tax', 'Invoice Value', 'Invoices'],
        ];
    }

    public function collection(): Collection
    {
        $rows = $this->data['rows']->map(fn ($r) => [
            $r->hsn_sac ?: '',
            $r->unit,
            $r->gst_rate,
            (float) $r->total_qty,
            $r->total_taxable,
            $r->total_cgst,
            $r->total_sgst,
            $r->total_igst,
            $r->total_tax,
            $r->total_amount,
            $r->inv_count,
        ]);

        $t = $this->data['totals'];
        $rows->push([]);
        $rows->push([
            'TOTAL', '', '', '',
            $t['taxable'], $t['cgst'], $t['sgst'], $t['igst'],
            $t['total_tax'], $t['grand'], '',
        ]);

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, 'B' => 8, 'C' => 12, 'D' => 12,
            'E' => 16, 'F' => 14, 'G' => 14, 'H' => 14,
            'I' => 14, 'J' => 16, 'K' => 10,
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

                foreach (['D', 'E', 'F', 'G', 'H', 'I', 'J'] as $col) {
                    $sheet->getStyle("{$col}5:{$col}{$lastRow}")
                          ->getNumberFormat()->setFormatCode('#,##0.00');
                }

                $sheet->getStyle("A{$lastRow}:K{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->freezePane('A5');
            },
        ];
    }
}

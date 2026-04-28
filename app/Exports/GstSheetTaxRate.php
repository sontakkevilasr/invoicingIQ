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

class GstSheetTaxRate implements
    FromCollection, WithHeadings, WithTitle,
    WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        private array  $data,
        private string $from,
        private string $to
    ) {}

    public function title(): string { return 'Tax by GST Rate'; }

    public function headings(): array
    {
        return [
            ['Tax Liability — By GST Rate'],
            ['Period: ' . $this->from . ' to ' . $this->to],
            [],
            ['GST Rate %', 'Taxable Value', 'CGST', 'SGST', 'IGST', 'Total Tax'],
        ];
    }

    public function collection(): Collection
    {
        $rows = $this->data['byRate']->map(fn ($r) => [
            $r->gst_rate . '%',
            $r->taxable, $r->cgst, $r->sgst, $r->igst, $r->total_tax,
        ]);

        $t = $this->data['totals'];
        $rows->push([]);
        $rows->push(['TOTAL', $t['taxable'], $t['cgst'], $t['sgst'], $t['igst'], $t['total_tax']]);

        return $rows;
    }

    public function columnWidths(): array
    {
        return ['A' => 14, 'B' => 16, 'C' => 14, 'D' => 14, 'E' => 14, 'F' => 14];
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

                foreach (['B', 'C', 'D', 'E', 'F'] as $col) {
                    $sheet->getStyle("{$col}5:{$col}{$lastRow}")
                          ->getNumberFormat()->setFormatCode('#,##0.00');
                }

                $sheet->getStyle("A{$lastRow}:F{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->freezePane('A5');
            },
        ];
    }
}

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

class GstSheetMonthly implements
    FromCollection, WithHeadings, WithTitle,
    WithStyles, WithColumnWidths, WithEvents
{
    public function __construct(
        private array  $data,
        private string $from,
        private string $to
    ) {}

    public function title(): string { return 'Month-wise Liability'; }

    public function headings(): array
    {
        return [
            ['Tax Liability — Month-wise Breakdown'],
            ['Period: ' . $this->from . ' to ' . $this->to],
            [],
            ['Month', 'Invoices', 'Taxable Value', 'CGST', 'SGST', 'IGST', 'Total Tax', 'Invoice Value'],
        ];
    }

    public function collection(): Collection
    {
        $monthly = $this->data['monthly'];

        $rows = $monthly->map(fn ($m) => [
            $m->month_label,
            $m->inv_count,
            $m->taxable, $m->cgst, $m->sgst, $m->igst,
            $m->total_tax, $m->grand,
        ]);

        $rows->push([]);
        $rows->push([
            'TOTAL',
            $monthly->sum('inv_count'),
            $monthly->sum('taxable'),
            $monthly->sum('cgst'),
            $monthly->sum('sgst'),
            $monthly->sum('igst'),
            $monthly->sum('total_tax'),
            $monthly->sum('grand'),
        ]);

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 14, 'B' => 10,
            'C' => 16, 'D' => 14, 'E' => 14, 'F' => 14,
            'G' => 14, 'H' => 16,
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

                foreach (['C', 'D', 'E', 'F', 'G', 'H'] as $col) {
                    $sheet->getStyle("{$col}5:{$col}{$lastRow}")
                          ->getNumberFormat()->setFormatCode('#,##0.00');
                }

                $sheet->getStyle("A{$lastRow}:H{$lastRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->freezePane('A5');
            },
        ];
    }
}

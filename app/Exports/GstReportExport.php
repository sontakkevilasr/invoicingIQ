<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GstReportExport implements WithMultipleSheets
{
    public function __construct(
        private string $reportType,
        private array  $data,
        private string $from,
        private string $to
    ) {}

    public function sheets(): array
    {
        return match ($this->reportType) {
            'gstr1'         => [
                new GstSheetGstr1($this->data, $this->from, $this->to),
            ],
            'b2b'           => [new GstSheetB2b($this->data, $this->from, $this->to)],
            'b2c'           => [new GstSheetB2c($this->data, $this->from, $this->to)],
            'hsn'           => [new GstSheetHsn($this->data, $this->from, $this->to)],
            'tax_liability' => [
                new GstSheetTaxRate($this->data, $this->from, $this->to),
                new GstSheetMonthly($this->data, $this->from, $this->to),
            ],
            default => [new GstSheetGstr1($this->data, $this->from, $this->to)],
        };
    }
}

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans',sans-serif; font-size:11px; color:#111; }
.page { padding:24px 28px; }

.report-header { border-bottom:2px solid #111318; padding-bottom:14px; margin-bottom:18px; }
.company-name { font-size:18px; font-weight:700; color:#111318; margin-bottom:4px; }
.company-detail { font-size:10px; color:#555; line-height:1.7; }
.report-title { font-size:15px; font-weight:700; color:#111318; margin-top:12px; }
.report-period { font-size:10px; color:#666; margin-top:2px; }

.stat-row { display:table; width:100%; border-collapse:collapse; margin-bottom:16px; background:#f6f7fb; border:1px solid #e2e5ed; border-radius:4px; }
.stat-cell { display:table-cell; padding:10px 14px; border-right:1px solid #e2e5ed; text-align:center; }
.stat-cell:last-child { border-right:none; }
.stat-label { font-size:9px; text-transform:uppercase; letter-spacing:.8px; color:#888; margin-bottom:3px; }
.stat-value { font-size:12px; font-weight:700; color:#111318; }

.section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#888; margin-bottom:6px; margin-top:14px; }
.badge { font-size:9px; font-weight:600; border-radius:10px; padding:2px 7px; display:inline-block; }
.badge-blue { background:#dbeafe; color:#1e40af; }
.badge-gray { background:#f1f5f9; color:#64748b; }

table { width:100%; border-collapse:collapse; margin-bottom:14px; font-size:10px; }
thead tr { background:#111318; }
thead th { padding:6px 8px; font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#fff; text-align:left; }
thead th.r { text-align:right; }
tbody tr { border-bottom:1px solid #eee; }
tbody tr:nth-child(even) { background:#fafafa; }
tbody td { padding:5px 8px; }
tbody td.r { text-align:right; font-variant-numeric:tabular-nums; }
tfoot tr { background:#f1f5f9; }
tfoot td { padding:6px 8px; font-weight:700; font-size:10px; border-top:1px solid #cbd5e1; }
tfoot td.r { text-align:right; font-variant-numeric:tabular-nums; }

.two-col { display:table; width:100%; border-spacing:12px; margin-bottom:14px; }
.col-left { display:table-cell; width:50%; vertical-align:top; }
.col-right { display:table-cell; width:50%; vertical-align:top; }

.bar-row { margin-bottom:12px; }
.bar-label { display:inline-block; width:40%; font-size:10px; color:#555; }
.bar-amount { display:inline-block; width:30%; text-align:right; font-size:10px; font-weight:600; }
.bar-pct { display:inline-block; width:25%; text-align:right; font-size:9px; color:#888; }
.bar-track { height:5px; background:#e2e5ed; border-radius:3px; margin-top:3px; }
.bar-fill { height:5px; border-radius:3px; }

.tax-summary { background:#fef4e6; border:1px solid #fcd34d; border-radius:4px; padding:8px 12px; margin-top:10px; }
.tax-summary-row { display:flex; justify-content:space-between; }
.tax-summary-label { font-size:11px; font-weight:600; }
.tax-summary-value { font-size:13px; font-weight:700; color:#92400e; }

.footer { margin-top:20px; padding-top:10px; border-top:1px solid #e2e5ed; display:table; width:100%; }
.footer-left { display:table-cell; font-size:9px; color:#888; }
.footer-right { display:table-cell; text-align:right; font-size:9px; color:#888; }

@page { margin:15mm; }
</style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="report-header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" style="max-height:52px;max-width:160px;object-fit:contain;display:block;margin-bottom:6px;">
        @endif
        <div class="company-name">{{ $settings['company_name'] ?? 'Company' }}</div>
        <div class="company-detail">
            {{ $settings['company_address'] ?? '' }}{{ $settings['company_city'] ? ', '.$settings['company_city'] : '' }}{{ $settings['company_state'] ? ', '.$settings['company_state'] : '' }}<br>
            @if($settings['company_gstin'] ?? '')GSTIN: {{ $settings['company_gstin'] }} &nbsp;|&nbsp; @endif
            @if($settings['company_phone'] ?? '')Phone: {{ $settings['company_phone'] }} &nbsp;|&nbsp; @endif
            {{ $settings['company_email'] ?? '' }}
        </div>
        <div class="report-title">{{ $reportLabels[$report] }}</div>
        <div class="report-period">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
    </div>

    {{-- Summary Stats --}}
    @php $t = $data['totals']; @endphp
    <div class="stat-row">
        <div class="stat-cell">
            <div class="stat-label">Taxable Value</div>
            <div class="stat-value">{{ fmt_inr($t['taxable']) }}</div>
        </div>
        <div class="stat-cell">
            <div class="stat-label">CGST</div>
            <div class="stat-value">{{ fmt_inr($t['cgst']) }}</div>
        </div>
        <div class="stat-cell">
            <div class="stat-label">SGST</div>
            <div class="stat-value">{{ fmt_inr($t['sgst']) }}</div>
        </div>
        <div class="stat-cell">
            <div class="stat-label">IGST</div>
            <div class="stat-value">{{ fmt_inr($t['igst']) }}</div>
        </div>
        <div class="stat-cell">
            <div class="stat-label">Total Tax</div>
            <div class="stat-value">{{ fmt_inr($t['total_tax']) }}</div>
        </div>
        @if(isset($t['grand']))
        <div class="stat-cell">
            <div class="stat-label">Grand Total</div>
            <div class="stat-value">{{ fmt_inr($t['grand']) }}</div>
        </div>
        @endif
    </div>

    {{-- ═══ GSTR-1 ═══ --}}
    @if($report === 'gstr1')

        <div class="section-title">B2B Invoices (Registered Customers)</div>
        @if(count($data['b2b']))
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th><th>Date</th><th>Customer</th>
                    <th>GSTIN</th><th>State</th>
                    <th class="r">Taxable</th><th class="r">CGST</th><th class="r">SGST</th>
                    <th class="r">IGST</th><th class="r">Total Tax</th><th class="r">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['b2b'] as $r)
                <tr>
                    <td>{{ $r['number'] }}</td>
                    <td>{{ $r['date'] }}</td>
                    <td>{{ $r['customer'] }}</td>
                    <td style="font-size:9px;">{{ $r['gstin'] }}</td>
                    <td style="font-size:9px;">{{ $r['state'] ?: '—' }}</td>
                    <td class="r">{{ fmt_inr($r['taxable'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['cgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['sgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['igst'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['total_tax'], false) }}</td>
                    <td class="r"><strong>{{ fmt_inr($r['grand'], false) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Subtotal — B2B ({{ count($data['b2b']) }} invoices)</td>
                    <td class="r">{{ fmt_inr(collect($data['b2b'])->sum('taxable'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2b'])->sum('cgst'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2b'])->sum('sgst'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2b'])->sum('igst'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2b'])->sum('total_tax'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2b'])->sum('grand'), false) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="font-size:10px;color:#888;margin-bottom:14px;">No B2B invoices in this period.</p>
        @endif

        <div class="section-title">B2C Invoices (Unregistered Customers)</div>
        @if(count($data['b2c']))
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th><th>Date</th><th>Customer</th><th>State</th>
                    <th class="r">Taxable</th><th class="r">CGST</th><th class="r">SGST</th>
                    <th class="r">IGST</th><th class="r">Total Tax</th><th class="r">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['b2c'] as $r)
                <tr>
                    <td>{{ $r['number'] }}</td>
                    <td>{{ $r['date'] }}</td>
                    <td>{{ $r['customer'] ?: '—' }}</td>
                    <td style="font-size:9px;">{{ $r['state'] ?: '—' }}</td>
                    <td class="r">{{ fmt_inr($r['taxable'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['cgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['sgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['igst'], false) }}</td>
                    <td class="r">{{ fmt_inr($r['total_tax'], false) }}</td>
                    <td class="r"><strong>{{ fmt_inr($r['grand'], false) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Subtotal — B2C ({{ count($data['b2c']) }} invoices)</td>
                    <td class="r">{{ fmt_inr(collect($data['b2c'])->sum('taxable'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2c'])->sum('cgst'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2c'])->sum('sgst'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2c'])->sum('igst'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2c'])->sum('total_tax'), false) }}</td>
                    <td class="r">{{ fmt_inr(collect($data['b2c'])->sum('grand'), false) }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="font-size:10px;color:#888;margin-bottom:14px;">No B2C invoices in this period.</p>
        @endif

    {{-- ═══ HSN ═══ --}}
    @elseif($report === 'hsn')
        <table>
            <thead>
                <tr>
                    <th>HSN/SAC</th><th>Unit</th><th class="r">GST%</th><th class="r">Qty</th>
                    <th class="r">Taxable</th><th class="r">CGST</th><th class="r">SGST</th>
                    <th class="r">IGST</th><th class="r">Total Tax</th><th class="r">Inv. Value</th><th class="r">Inv#</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['rows'] as $r)
                <tr>
                    <td style="font-size:9px;">{{ $r->hsn_sac ?: '—' }}</td>
                    <td style="font-size:9px;">{{ $r->unit }}</td>
                    <td class="r">{{ $r->gst_rate }}%</td>
                    <td class="r">{{ number_format($r->total_qty, 2) }}</td>
                    <td class="r">{{ fmt_inr($r->total_taxable, false) }}</td>
                    <td class="r">{{ fmt_inr($r->total_cgst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->total_sgst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->total_igst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->total_tax, false) }}</td>
                    <td class="r"><strong>{{ fmt_inr($r->total_amount, false) }}</strong></td>
                    <td class="r" style="color:#888;">{{ $r->inv_count }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">Total ({{ $data['rows']->count() }} HSN codes)</td>
                    <td class="r">{{ fmt_inr($t['taxable'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['cgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['sgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['igst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['total_tax'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['grand'], false) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

    {{-- ═══ TAX LIABILITY ═══ --}}
    @elseif($report === 'tax_liability')

        <div class="two-col">
            <div class="col-left">
                <div class="section-title">Tax by GST Rate</div>
                <table>
                    <thead>
                        <tr>
                            <th>GST Rate</th><th class="r">Taxable</th><th class="r">CGST</th>
                            <th class="r">SGST</th><th class="r">IGST</th><th class="r">Total Tax</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['byRate'] as $r)
                        <tr>
                            <td>{{ $r->gst_rate }}%</td>
                            <td class="r">{{ fmt_inr($r->taxable, false) }}</td>
                            <td class="r">{{ fmt_inr($r->cgst, false) }}</td>
                            <td class="r">{{ fmt_inr($r->sgst, false) }}</td>
                            <td class="r">{{ fmt_inr($r->igst, false) }}</td>
                            <td class="r"><strong>{{ fmt_inr($r->total_tax, false) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td class="r">{{ fmt_inr($t['taxable'], false) }}</td>
                            <td class="r">{{ fmt_inr($t['cgst'], false) }}</td>
                            <td class="r">{{ fmt_inr($t['sgst'], false) }}</td>
                            <td class="r">{{ fmt_inr($t['igst'], false) }}</td>
                            <td class="r">{{ fmt_inr($t['total_tax'], false) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-right">
                <div class="section-title">Tax Composition</div>
                @php
                    $total = $t['total_tax'];
                    $components = [
                        ['CGST', $t['cgst'], '#1e40af'],
                        ['SGST', $t['sgst'], '#0369a1'],
                        ['IGST', $t['igst'], '#b45309'],
                    ];
                @endphp
                @foreach($components as [$lbl, $val, $col])
                    @php $pct = $total > 0 ? round($val / $total * 100, 1) : 0; @endphp
                    <div class="bar-row">
                        <span class="bar-label">{{ $lbl }}</span>
                        <span class="bar-amount">{{ fmt_inr($val, false) }}</span>
                        <span class="bar-pct">{{ $pct }}%</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ $pct }}%;background:{{ $col }};"></div>
                        </div>
                    </div>
                @endforeach
                <div class="tax-summary">
                    <div class="tax-summary-row">
                        <span class="tax-summary-label">Total Tax Liability</span>
                        <span class="tax-summary-value">{{ fmt_inr($t['total_tax']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-title">Month-wise Tax Liability</div>
        <table>
            <thead>
                <tr>
                    <th>Month</th><th class="r">Invoices</th><th class="r">Taxable</th>
                    <th class="r">CGST</th><th class="r">SGST</th><th class="r">IGST</th>
                    <th class="r">Total Tax</th><th class="r">Invoice Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['monthly'] as $m)
                <tr>
                    <td>{{ $m->month_label }}</td>
                    <td class="r">{{ $m->inv_count }}</td>
                    <td class="r">{{ fmt_inr($m->taxable, false) }}</td>
                    <td class="r">{{ fmt_inr($m->cgst, false) }}</td>
                    <td class="r">{{ fmt_inr($m->sgst, false) }}</td>
                    <td class="r">{{ fmt_inr($m->igst, false) }}</td>
                    <td class="r"><strong>{{ fmt_inr($m->total_tax, false) }}</strong></td>
                    <td class="r"><strong>{{ fmt_inr($m->grand, false) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td>Total</td>
                    <td class="r">{{ $data['monthly']->sum('inv_count') }}</td>
                    <td class="r">{{ fmt_inr($data['monthly']->sum('taxable'), false) }}</td>
                    <td class="r">{{ fmt_inr($data['monthly']->sum('cgst'), false) }}</td>
                    <td class="r">{{ fmt_inr($data['monthly']->sum('sgst'), false) }}</td>
                    <td class="r">{{ fmt_inr($data['monthly']->sum('igst'), false) }}</td>
                    <td class="r">{{ fmt_inr($data['monthly']->sum('total_tax'), false) }}</td>
                    <td class="r">{{ fmt_inr($data['monthly']->sum('grand'), false) }}</td>
                </tr>
            </tfoot>
        </table>

    {{-- ═══ B2B ═══ --}}
    @elseif($report === 'b2b')
        <table>
            <thead>
                <tr>
                    <th>Customer</th><th>GSTIN</th><th>State</th><th class="r">Invoices</th>
                    <th class="r">Taxable</th><th class="r">CGST</th><th class="r">SGST</th>
                    <th class="r">IGST</th><th class="r">Total Tax</th><th class="r">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['rows'] as $r)
                <tr>
                    <td>{{ $r->customer_name }}</td>
                    <td style="font-size:9px;">{{ $r->customer_gstin }}</td>
                    <td style="font-size:9px;">{{ $r->customer_state ?: '—' }}</td>
                    <td class="r">{{ $r->inv_count }}</td>
                    <td class="r">{{ fmt_inr($r->taxable, false) }}</td>
                    <td class="r">{{ fmt_inr($r->cgst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->sgst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->igst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->total_tax, false) }}</td>
                    <td class="r"><strong>{{ fmt_inr($r->grand, false) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3">Total ({{ $data['rows']->count() }} customers)</td>
                    <td class="r">{{ $t['invoices'] }}</td>
                    <td class="r">{{ fmt_inr($t['taxable'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['cgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['sgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['igst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['total_tax'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['grand'], false) }}</td>
                </tr>
            </tfoot>
        </table>

    {{-- ═══ B2C ═══ --}}
    @elseif($report === 'b2c')
        <table>
            <thead>
                <tr>
                    <th>State</th><th>Tax Type</th><th class="r">Invoices</th>
                    <th class="r">Taxable</th><th class="r">CGST</th><th class="r">SGST</th>
                    <th class="r">IGST</th><th class="r">Total Tax</th><th class="r">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['rows'] as $r)
                <tr>
                    <td>{{ $r->customer_state ?: 'Not specified' }}</td>
                    <td>{{ $r->is_intra_state ? 'CGST + SGST' : 'IGST' }}</td>
                    <td class="r">{{ $r->inv_count }}</td>
                    <td class="r">{{ fmt_inr($r->taxable, false) }}</td>
                    <td class="r">{{ fmt_inr($r->cgst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->sgst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->igst, false) }}</td>
                    <td class="r">{{ fmt_inr($r->total_tax, false) }}</td>
                    <td class="r"><strong>{{ fmt_inr($r->grand, false) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total ({{ $data['rows']->count() }} states)</td>
                    <td class="r">{{ $t['invoices'] }}</td>
                    <td class="r">{{ fmt_inr($t['taxable'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['cgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['sgst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['igst'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['total_tax'], false) }}</td>
                    <td class="r">{{ fmt_inr($t['grand'], false) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">{{ $settings['company_name'] ?? '' }} &nbsp;|&nbsp; GSTIN: {{ $settings['company_gstin'] ?? '' }}</div>
        <div class="footer-right">Generated on {{ now()->format('d M Y, h:i A') }}</div>
    </div>

</div>
</body>
</html>

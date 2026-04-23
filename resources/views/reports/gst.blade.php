@extends('layouts.app')
@section('title', 'GST Reports')

@section('content')
<div class="page" style="max-width:1200px;">

    {{-- Page heading --}}
    <div class="page-head flex justify-between items-center">
        <div>
            <div class="page-title">GST Reports</div>
            <div class="page-subtitle">
                {{ \Carbon\Carbon::parse($from)->format('d M Y') }}
                &nbsp;—&nbsp;
                {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
            </div>
        </div>
        <button onclick="window.print()" class="btn no-print">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none">
                <path d="M4 6V2h8v4M4 12H2V7h12v5h-2M4 10h8v4H4v-4z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
            </svg>
            Print / Export
        </button>
    </div>

    {{-- ── Filters bar ──────────────────────────────────── --}}
    <form method="GET" action="{{ route('reports.gst') }}" id="filterForm" class="no-print">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:22px;padding:16px 18px;background:var(--surface);border:1px solid var(--bdr);border-radius:var(--radius-lg);">

            {{-- Report type --}}
            <div>
                <div class="form-label">Report Type</div>
                <select name="report" class="form-control" style="width:180px;" onchange="this.form.submit()">
                    @foreach([
                        'gstr1'          => 'GSTR-1 Summary',
                        'b2b'            => 'B2B (Registered)',
                        'b2c'            => 'B2C (Unregistered)',
                        'hsn'            => 'HSN-wise Summary',
                        'tax_liability'  => 'Tax Liability',
                    ] as $val => $lbl)
                        <option value="{{ $val }}" {{ $report === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Quick periods --}}
            <div>
                <div class="form-label">Period</div>
                <select name="period" class="form-control" style="width:160px;" onchange="this.form.submit()">
                    @foreach([
                        'this_month'   => 'This Month',
                        'last_month'   => 'Last Month',
                        'this_quarter' => 'This Quarter',
                        'last_quarter' => 'Last Quarter',
                        'this_year'    => 'This Year',
                        'last_year'    => 'Last Year',
                        'custom'       => 'Custom Range',
                    ] as $val => $lbl)
                        <option value="{{ $val }}" {{ $period === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date range (shown for custom) --}}
            <div id="dateRangeWrap" style="{{ $period !== 'custom' ? 'opacity:.45;pointer-events:none;' : '' }}display:flex;gap:8px;align-items:flex-end;">
                <div>
                    <div class="form-label">From</div>
                    <input type="date" name="from" value="{{ $from }}" class="form-control" style="width:150px;">
                </div>
                <div>
                    <div class="form-label">To</div>
                    <input type="date" name="to" value="{{ $to }}" class="form-control" style="width:150px;">
                </div>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </div>
    </form>

    {{-- ── Summary stat cards ───────────────────────────── --}}
    @php
        $t = $data['totals'];
    @endphp
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:22px;">
        <div class="stat-card">
            <div class="stat-label">Taxable Value</div>
            <div class="stat-value" style="font-size:18px;">{{ fmt_inr($t['taxable']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">CGST</div>
            <div class="stat-value" style="font-size:18px;color:var(--accent);">{{ fmt_inr($t['cgst']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">SGST</div>
            <div class="stat-value" style="font-size:18px;color:var(--accent);">{{ fmt_inr($t['sgst']) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">IGST</div>
            <div class="stat-value" style="font-size:18px;color:var(--warn);">{{ fmt_inr($t['igst']) }}</div>
        </div>
        <div class="stat-card" style="border-color:var(--bdr2);">
            <div class="stat-label">Total Tax</div>
            <div class="stat-value" style="font-size:18px;color:var(--err);">{{ fmt_inr($t['total_tax']) }}</div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- GSTR-1 SUMMARY                                      --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @if($report === 'gstr1')
        {{-- B2B --}}
        <div class="card" style="margin-bottom:18px;">
            <div class="card-header">
                <div class="card-title">
                    B2B Invoices — Registered Customers
                    <span class="badge badge-blue" style="margin-left:8px;">{{ count($data['b2b']) }} invoices</span>
                </div>
                <span style="font-size:11px;color:var(--t4);">Customers with GSTIN</span>
            </div>
            @if(count($data['b2b']))
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Invoice #</th><th>Date</th><th>Customer</th><th>GSTIN</th>
                            <th>State</th><th class="r">Taxable</th><th class="r">CGST</th>
                            <th class="r">SGST</th><th class="r">IGST</th><th class="r">Total Tax</th>
                            <th class="r">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['b2b'] as $r)
                        <tr>
                            <td><a href="{{ route('invoices.edit', $r['id']) }}" style="color:var(--accent);font-weight:600;text-decoration:none;">{{ $r['number'] }}</a></td>
                            <td style="color:var(--t3);">{{ $r['date'] }}</td>
                            <td style="font-weight:500;">{{ $r['customer'] }}</td>
                            <td class="font-mono" style="font-size:11px;color:var(--t3);">{{ $r['gstin'] }}</td>
                            <td style="font-size:11px;color:var(--t3);">{{ $r['state'] ?: '—' }}</td>
                            <td class="r">{{ fmt_inr($r['taxable']) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r['cgst']) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r['sgst']) }}</td>
                            <td class="r" style="color:var(--warn);">{{ fmt_inr($r['igst']) }}</td>
                            <td class="r" style="font-weight:500;">{{ fmt_inr($r['total_tax']) }}</td>
                            <td class="r" style="font-weight:600;">{{ fmt_inr($r['grand']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:var(--s2);">
                        <tr>
                            <td colspan="5" style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--t3);">SUBTOTAL — B2B</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;">{{ fmt_inr(collect($data['b2b'])->sum('taxable')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;color:var(--accent);">{{ fmt_inr(collect($data['b2b'])->sum('cgst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;color:var(--accent);">{{ fmt_inr(collect($data['b2b'])->sum('sgst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;color:var(--warn);">{{ fmt_inr(collect($data['b2b'])->sum('igst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;">{{ fmt_inr(collect($data['b2b'])->sum('total_tax')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr(collect($data['b2b'])->sum('grand')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div style="padding:30px;text-align:center;color:var(--t4);">No B2B invoices in this period.</div>
            @endif
        </div>

        {{-- B2C --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    B2C Invoices — Unregistered Customers
                    <span class="badge badge-gray" style="margin-left:8px;">{{ count($data['b2c']) }} invoices</span>
                </div>
                <span style="font-size:11px;color:var(--t4);">Customers without GSTIN</span>
            </div>
            @if(count($data['b2c']))
            <div class="table-wrap">
                <table class="tbl">
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
                            <td><a href="{{ route('invoices.edit', $r['id']) }}" style="color:var(--accent);font-weight:600;text-decoration:none;">{{ $r['number'] }}</a></td>
                            <td style="color:var(--t3);">{{ $r['date'] }}</td>
                            <td style="font-weight:500;">{{ $r['customer'] ?: '—' }}</td>
                            <td style="font-size:11px;color:var(--t3);">{{ $r['state'] ?: '—' }}</td>
                            <td class="r">{{ fmt_inr($r['taxable']) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r['cgst']) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r['sgst']) }}</td>
                            <td class="r" style="color:var(--warn);">{{ fmt_inr($r['igst']) }}</td>
                            <td class="r" style="font-weight:500;">{{ fmt_inr($r['total_tax']) }}</td>
                            <td class="r" style="font-weight:600;">{{ fmt_inr($r['grand']) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:var(--s2);">
                        <tr>
                            <td colspan="4" style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--t3);">SUBTOTAL — B2C</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;">{{ fmt_inr(collect($data['b2c'])->sum('taxable')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;color:var(--accent);">{{ fmt_inr(collect($data['b2c'])->sum('cgst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;color:var(--accent);">{{ fmt_inr(collect($data['b2c'])->sum('sgst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;color:var(--warn);">{{ fmt_inr(collect($data['b2c'])->sum('igst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;">{{ fmt_inr(collect($data['b2c'])->sum('total_tax')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr(collect($data['b2c'])->sum('grand')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div style="padding:30px;text-align:center;color:var(--t4);">No B2C invoices in this period.</div>
            @endif
        </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- HSN SUMMARY                                          --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @elseif($report === 'hsn')
        <div class="card">
            <div class="card-header">
                <div class="card-title">HSN / SAC Wise Summary</div>
                <span style="font-size:11px;color:var(--t4);">{{ $data['rows']->count() }} HSN codes</span>
            </div>
            @if($data['rows']->count())
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>HSN / SAC</th><th>Unit</th><th class="r">GST %</th>
                            <th class="r">Qty</th><th class="r">Taxable Value</th>
                            <th class="r">CGST</th><th class="r">SGST</th><th class="r">IGST</th>
                            <th class="r">Total Tax</th><th class="r">Invoice Value</th><th class="r">Invoices</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['rows'] as $r)
                        <tr>
                            <td class="font-mono" style="font-size:12px;font-weight:500;">{{ $r->hsn_sac ?: '—' }}</td>
                            <td style="font-size:12px;color:var(--t3);">{{ $r->unit }}</td>
                            <td class="r"><span class="badge badge-blue">{{ $r->gst_rate }}%</span></td>
                            <td class="r" style="font-variant-numeric:tabular-nums;">{{ number_format($r->total_qty, 2) }}</td>
                            <td class="r" style="font-weight:500;">{{ fmt_inr($r->total_taxable) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r->total_cgst) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r->total_sgst) }}</td>
                            <td class="r" style="color:var(--warn);">{{ fmt_inr($r->total_igst) }}</td>
                            <td class="r" style="font-weight:500;">{{ fmt_inr($r->total_tax) }}</td>
                            <td class="r" style="font-weight:600;">{{ fmt_inr($r->total_amount) }}</td>
                            <td class="r" style="color:var(--t3);">{{ $r->inv_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:var(--s2);">
                        <tr>
                            <td colspan="4" style="padding:10px 16px;font-size:11px;font-weight:600;color:var(--t3);">TOTAL</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['taxable']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['cgst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['sgst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--warn);">{{ fmt_inr($data['totals']['igst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['total_tax']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['grand']) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div style="padding:30px;text-align:center;color:var(--t4);">No data for this period.</div>
            @endif
        </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- TAX LIABILITY                                        --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @elseif($report === 'tax_liability')

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;">

            {{-- By GST Rate --}}
            <div class="card">
                <div class="card-header"><div class="card-title">Tax by GST Rate</div></div>
                @if($data['byRate']->count())
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>GST Rate</th>
                                <th class="r">Taxable</th>
                                <th class="r">CGST</th>
                                <th class="r">SGST</th>
                                <th class="r">IGST</th>
                                <th class="r">Total Tax</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['byRate'] as $r)
                            <tr>
                                <td><span class="badge badge-blue">{{ $r->gst_rate }}%</span></td>
                                <td class="r">{{ fmt_inr($r->taxable) }}</td>
                                <td class="r" style="color:var(--accent);">{{ fmt_inr($r->cgst) }}</td>
                                <td class="r" style="color:var(--accent);">{{ fmt_inr($r->sgst) }}</td>
                                <td class="r" style="color:var(--warn);">{{ fmt_inr($r->igst) }}</td>
                                <td class="r" style="font-weight:600;">{{ fmt_inr($r->total_tax) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background:var(--s2);">
                            <tr>
                                <td style="padding:10px 16px;font-size:11px;font-weight:600;">TOTAL</td>
                                <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['taxable']) }}</td>
                                <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['cgst']) }}</td>
                                <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['sgst']) }}</td>
                                <td class="r" style="padding:10px 6px;font-weight:700;color:var(--warn);">{{ fmt_inr($data['totals']['igst']) }}</td>
                                <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['total_tax']) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                    <div style="padding:30px;text-align:center;color:var(--t4);">No data.</div>
                @endif
            </div>

            {{-- Visual: Tax breakdown pie-ish --}}
            <div class="card" style="padding:20px;">
                <div class="card-title" style="margin-bottom:16px;">Tax Composition</div>
                @php
                    $totalTax = $data['totals']['total_tax'];
                    $cgst     = $data['totals']['cgst'];
                    $sgst     = $data['totals']['sgst'];
                    $igst     = $data['totals']['igst'];
                @endphp
                @foreach([['CGST',$cgst,'var(--accent)'],['SGST',$sgst,'#3b82f6'],['IGST',$igst,'var(--warn)']] as [$lbl,$val,$col])
                    @php $pct = $totalTax > 0 ? round($val / $totalTax * 100, 1) : 0; @endphp
                    <div style="margin-bottom:14px;">
                        <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:4px;">
                            <span style="font-size:12px;font-weight:500;color:var(--t2);">{{ $lbl }}</span>
                            <span style="font-size:12px;font-weight:600;color:var(--t1);font-variant-numeric:tabular-nums;">
                                {{ fmt_inr($val) }} <span style="font-size:10px;color:var(--t4);">({{ $pct }}%)</span>
                            </span>
                        </div>
                        <div style="height:7px;background:var(--s3);border-radius:4px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:{{ $col }};border-radius:4px;transition:width .5s;"></div>
                        </div>
                    </div>
                @endforeach
                <div style="margin-top:20px;padding:12px;background:var(--s2);border-radius:var(--radius);border:1px solid var(--bdr);">
                    <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;">
                        <span>Total Tax Liability</span>
                        <span style="color:var(--err);">{{ fmt_inr($totalTax) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Month-wise --}}
        <div class="card">
            <div class="card-header"><div class="card-title">Month-wise Tax Liability</div></div>
            @if($data['monthly']->count())
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Month</th><th class="r">Invoices</th>
                            <th class="r">Taxable Value</th>
                            <th class="r">CGST</th><th class="r">SGST</th><th class="r">IGST</th>
                            <th class="r">Total Tax</th><th class="r">Invoice Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['monthly'] as $m)
                        <tr>
                            <td style="font-weight:500;">{{ $m->month_label }}</td>
                            <td class="r" style="color:var(--t3);">{{ $m->inv_count }}</td>
                            <td class="r">{{ fmt_inr($m->taxable) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($m->cgst) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($m->sgst) }}</td>
                            <td class="r" style="color:var(--warn);">{{ fmt_inr($m->igst) }}</td>
                            <td class="r" style="font-weight:600;">{{ fmt_inr($m->total_tax) }}</td>
                            <td class="r" style="font-weight:600;">{{ fmt_inr($m->grand) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:var(--s2);">
                        <tr>
                            <td style="padding:10px 16px;font-size:11px;font-weight:600;">TOTAL</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;">{{ $data['monthly']->sum('inv_count') }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['monthly']->sum('taxable')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['monthly']->sum('cgst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['monthly']->sum('sgst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--warn);">{{ fmt_inr($data['monthly']->sum('igst')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['monthly']->sum('total_tax')) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['monthly']->sum('grand')) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div style="padding:30px;text-align:center;color:var(--t4);">No data for this period.</div>
            @endif
        </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- B2B DETAILED                                         --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @elseif($report === 'b2b')
        <div class="card">
            <div class="card-header">
                <div class="card-title">B2B — Customer-wise Summary</div>
                <span style="font-size:11px;color:var(--t4);">{{ $data['rows']->count() }} registered customers</span>
            </div>
            @if($data['rows']->count())
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Customer</th><th>GSTIN</th><th>State</th>
                            <th class="r">Invoices</th><th class="r">Taxable</th>
                            <th class="r">CGST</th><th class="r">SGST</th><th class="r">IGST</th>
                            <th class="r">Total Tax</th><th class="r">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['rows'] as $r)
                        <tr>
                            <td style="font-weight:500;">{{ $r->customer_name }}</td>
                            <td class="font-mono" style="font-size:11px;color:var(--t3);">{{ $r->customer_gstin }}</td>
                            <td style="font-size:11px;color:var(--t3);">{{ $r->customer_state ?: '—' }}</td>
                            <td class="r" style="color:var(--t3);">{{ $r->inv_count }}</td>
                            <td class="r">{{ fmt_inr($r->taxable) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r->cgst) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r->sgst) }}</td>
                            <td class="r" style="color:var(--warn);">{{ fmt_inr($r->igst) }}</td>
                            <td class="r" style="font-weight:500;">{{ fmt_inr($r->total_tax) }}</td>
                            <td class="r" style="font-weight:700;">{{ fmt_inr($r->grand) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:var(--s2);">
                        <tr>
                            <td colspan="3" style="padding:10px 16px;font-size:11px;font-weight:600;">TOTAL</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;">{{ $data['totals']['invoices'] }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['taxable']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['cgst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['sgst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--warn);">{{ fmt_inr($data['totals']['igst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['total_tax']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['grand']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div style="padding:30px;text-align:center;color:var(--t4);">No B2B invoices in this period.</div>
            @endif
        </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- B2C DETAILED                                         --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    @elseif($report === 'b2c')
        <div class="card">
            <div class="card-header">
                <div class="card-title">B2C — State-wise Summary</div>
                <span style="font-size:11px;color:var(--t4);">Unregistered customers grouped by state</span>
            </div>
            @if($data['rows']->count())
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>State</th><th>Tax Type</th>
                            <th class="r">Invoices</th><th class="r">Taxable</th>
                            <th class="r">CGST</th><th class="r">SGST</th><th class="r">IGST</th>
                            <th class="r">Total Tax</th><th class="r">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['rows'] as $r)
                        <tr>
                            <td style="font-weight:500;">{{ $r->customer_state ?: 'Not specified' }}</td>
                            <td>
                                @if($r->is_intra_state)
                                    <span class="badge badge-green">CGST+SGST</span>
                                @else
                                    <span class="badge badge-orange">IGST</span>
                                @endif
                            </td>
                            <td class="r" style="color:var(--t3);">{{ $r->inv_count }}</td>
                            <td class="r">{{ fmt_inr($r->taxable) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r->cgst) }}</td>
                            <td class="r" style="color:var(--accent);">{{ fmt_inr($r->sgst) }}</td>
                            <td class="r" style="color:var(--warn);">{{ fmt_inr($r->igst) }}</td>
                            <td class="r" style="font-weight:500;">{{ fmt_inr($r->total_tax) }}</td>
                            <td class="r" style="font-weight:700;">{{ fmt_inr($r->grand) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:var(--s2);">
                        <tr>
                            <td colspan="2" style="padding:10px 16px;font-size:11px;font-weight:600;">TOTAL</td>
                            <td class="r" style="padding:10px 6px;font-weight:600;">{{ $data['totals']['invoices'] }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['taxable']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['cgst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--accent);">{{ fmt_inr($data['totals']['sgst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;color:var(--warn);">{{ fmt_inr($data['totals']['igst']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['total_tax']) }}</td>
                            <td class="r" style="padding:10px 6px;font-weight:700;">{{ fmt_inr($data['totals']['grand']) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
                <div style="padding:30px;text-align:center;color:var(--t4);">No B2C invoices in this period.</div>
            @endif
        </div>
    @endif

</div>

@push('styles')
<style>
@media print {
    .no-print { display: none !important; }
    .page { padding: 0; }
    .card { box-shadow: none; border: 1px solid #ddd; }
}
</style>
@endpush

@push('scripts')
<script>
document.querySelector('[name="period"]')?.addEventListener('change', function() {
    const wrap = document.getElementById('dateRangeWrap');
    if (this.value === 'custom') {
        wrap.style.opacity = '1';
        wrap.style.pointerEvents = 'auto';
    } else {
        wrap.style.opacity = '.45';
        wrap.style.pointerEvents = 'none';
    }
});
</script>
@endpush
@endsection

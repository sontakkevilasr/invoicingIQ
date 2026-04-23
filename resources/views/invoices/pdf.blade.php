<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #111; }
.page { padding: 30px 36px; }
.header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 2px solid #111318; }
.co-name { font-size: 22px; font-weight: 700; color: #111318; margin-bottom: 6px; }
.co-detail { font-size: 11px; color: #555; line-height: 1.8; }
.inv-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 3px; }
.inv-number { font-size: 20px; font-weight: 700; color: #111318; text-align: right; }
.status-chip { display: inline-block; margin-top: 8px; font-size: 10px; font-weight: 600; text-transform: uppercase; padding: 3px 10px; border-radius: 20px; background: #eceef4; color: #555; }
.dates { margin-top: 10px; display: flex; gap: 20px; }
.date-block { text-align: right; }
.date-block .dl { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 2px; }
.date-block .dv { font-size: 12px; font-weight: 500; }

.parties { display: flex; gap: 24px; margin-bottom: 20px; }
.party { flex: 1; }
.party-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #888; margin-bottom: 6px; }
.party-name { font-size: 13px; font-weight: 600; margin-bottom: 3px; }
.party-detail { font-size: 11px; color: #555; line-height: 1.8; }
.party-gstin { font-size: 10px; color: #1a56db; font-weight: 500; margin-top: 4px; }

table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
thead tr { background: #111318; }
thead th { padding: 8px 10px; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: .6px; color: #fff; text-align: left; }
thead th.r { text-align: right; }
tbody tr { border-bottom: 1px solid #eee; }
tbody tr:nth-child(even) { background: #fafafa; }
tbody td { padding: 8px 10px; font-size: 11px; }
tbody td.r { text-align: right; }
tfoot td { padding: 6px 10px; font-size: 11px; }

.totals-wrap { display: flex; justify-content: flex-end; }
.totals-table { width: 280px; }
.totals-table td { padding: 4px 8px; font-size: 11px; }
.totals-table .tl { color: #555; }
.totals-table .tv { text-align: right; font-weight: 500; font-variant-numeric: tabular-nums; }
.totals-table .grand td { font-size: 14px; font-weight: 700; border-top: 2px solid #111318; padding-top: 8px; }
.gst-box { background: #f6f7fb; border: 1px solid #e2e5ed; border-radius: 6px; padding: 8px 12px; margin-bottom: 8px; }
.gst-row { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; }
.words-box { background: #fef4e6; border-radius: 5px; padding: 6px 10px; font-size: 10px; color: #a35c08; font-style: italic; margin-top: 8px; }

.footer-wrap { display: flex; gap: 24px; margin-top: 24px; border-top: 1px solid #eee; padding-top: 16px; }
.bank-box { flex: 1; }
.bank-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 5px; }
.bank-detail { font-size: 11px; color: #444; line-height: 1.9; }
.sign-box { width: 200px; text-align: right; }
.sign-label { font-size: 10px; color: #888; }
.sign-name { font-size: 12px; font-weight: 600; margin-top: 30px; }
.notes-box { margin-top: 16px; }
.notes-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 4px; }
.notes-text { font-size: 11px; color: #555; }
</style>
</head>
<body>
<div class="page">
    {{-- Header --}}
    <div class="header">
        <div>
            <div class="co-name">{{ $settings['company_name'] ?? '' }}</div>
            <div class="co-detail">
                {{ $settings['company_address'] ?? '' }}, {{ $settings['company_city'] ?? '' }}, {{ $settings['company_state'] ?? '' }}<br>
                Phone: {{ $settings['company_phone'] ?? '' }} &nbsp;|&nbsp; {{ $settings['company_email'] ?? '' }}<br>
                GSTIN: {{ $settings['company_gstin'] ?? '' }} &nbsp;|&nbsp; PAN: {{ $settings['company_pan'] ?? '' }}
            </div>
        </div>
        <div>
            <div class="inv-label">Tax Invoice</div>
            <div class="inv-number">{{ $invoice->number }}</div>
            <div><span class="status-chip">{{ ucfirst($invoice->effective_status) }}</span></div>
            <div class="dates">
                <div class="date-block">
                    <div class="dl">Invoice Date</div>
                    <div class="dv">{{ $invoice->invoice_date->format('d M Y') }}</div>
                </div>
                @if($invoice->due_date)
                <div class="date-block">
                    <div class="dl">Due Date</div>
                    <div class="dv">{{ $invoice->due_date->format('d M Y') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Parties --}}
    <div class="parties">
        <div class="party">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $invoice->customer_name }}</div>
            <div class="party-detail">
                {{ $invoice->customer_billing_address }}<br>
                {{ $invoice->customer_city }}, {{ $invoice->customer_state }}
            </div>
            @if($invoice->customer_gstin)
                <div class="party-gstin">GSTIN: {{ $invoice->customer_gstin }}</div>
            @endif
        </div>
        <div class="party">
            <div class="party-label">Place of Supply</div>
            <div class="party-detail">{{ $invoice->place_of_supply ?: $invoice->customer_state }}</div>
            <div style="margin-top:8px;font-size:10px;font-weight:600;color:{{ $invoice->is_intra_state ? '#0c7a59' : '#1a56db' }};">
                {{ $invoice->is_intra_state ? 'CGST + SGST Applicable' : 'IGST Applicable' }}
            </div>
        </div>
    </div>

    {{-- Line Items --}}
    <table>
        <thead>
            <tr>
                <th style="width:28px;">#</th>
                <th>Item / Service</th>
                <th>HSN/SAC</th>
                <th class="r">Qty</th>
                <th class="r">Rate (₹)</th>
                <th class="r">Disc%</th>
                <th class="r">Taxable (₹)</th>
                <th class="r">GST%</th>
                <th class="r">Tax (₹)</th>
                <th class="r">Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>
                    <strong>{{ $item->item_name }}</strong>
                    @if($item->description)<br><small style="color:#777;">{{ $item->description }}</small>@endif
                </td>
                <td>{{ $item->hsn_sac ?: '—' }}</td>
                <td class="r">{{ rtrim(rtrim(number_format($item->qty,3),'0'),'.') }} {{ $item->unit }}</td>
                <td class="r">{{ fmt_inr($item->rate) }}</td>
                <td class="r">{{ $item->discount_percent > 0 ? $item->discount_percent.'%' : '—' }}</td>
                <td class="r">{{ fmt_inr($item->taxable_amount) }}</td>
                <td class="r">{{ $item->gst_rate }}%</td>
                <td class="r">{{ fmt_inr($item->total_tax) }}</td>
                <td class="r"><strong>{{ fmt_inr($item->total_amount) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-wrap">
        <table class="totals-table">
            <tr><td class="tl">Subtotal</td><td class="tv">{{ fmt_inr($invoice->sub_total) }}</td></tr>
            @if($invoice->discount_amount > 0)
            <tr><td class="tl">Discount</td><td class="tv" style="color:#0c7a59;">-{{ fmt_inr($invoice->discount_amount) }}</td></tr>
            @endif
            <tr>
                <td colspan="2">
                    <div class="gst-box">
                        @if($invoice->is_intra_state)
                            <div class="gst-row"><span>CGST</span><span>{{ fmt_inr($invoice->total_cgst) }}</span></div>
                            <div class="gst-row"><span>SGST</span><span>{{ fmt_inr($invoice->total_sgst) }}</span></div>
                        @else
                            <div class="gst-row"><span>IGST</span><span>{{ fmt_inr($invoice->total_igst) }}</span></div>
                        @endif
                        <div class="gst-row" style="font-weight:600;border-top:1px solid #ddd;margin-top:4px;padding-top:4px;">
                            <span>Total Tax</span><span>{{ fmt_inr($invoice->total_tax) }}</span>
                        </div>
                    </div>
                </td>
            </tr>
            @if(abs($invoice->round_off) > 0)
            <tr><td class="tl">Round Off</td><td class="tv">{{ $invoice->round_off >= 0 ? '+' : '' }}{{ fmt_inr($invoice->round_off) }}</td></tr>
            @endif
            <tr class="grand"><td class="tl">Grand Total</td><td class="tv">{{ fmt_inr($invoice->grand_total) }}</td></tr>
        </table>
    </div>
    <div class="words-box">{{ number_to_words($invoice->grand_total) }}</div>

    {{-- Footer --}}
    <div class="footer-wrap">
        <div class="bank-box">
            <div class="bank-title">Bank Details</div>
            <div class="bank-detail">
                {{ $settings['bank_name'] ?? '' }} &nbsp;|&nbsp; A/C: {{ $settings['bank_acc_no'] ?? '' }}<br>
                IFSC: {{ $settings['bank_ifsc'] ?? '' }} &nbsp;|&nbsp; Branch: {{ $settings['bank_branch'] ?? '' }}
            </div>
        </div>
        <div class="sign-box">
            <div class="sign-label">For {{ $settings['company_name'] ?? '' }}</div>
            <div class="sign-name">Authorised Signatory</div>
        </div>
    </div>

    @if($invoice->notes)
    <div class="notes-box">
        <div class="notes-label">Notes</div>
        <div class="notes-text">{{ $invoice->notes }}</div>
    </div>
    @endif
</div>
</body>
</html>

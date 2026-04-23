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
    
    <div class="header">
        <div>
            <div class="co-name"><?php echo e($settings['company_name'] ?? ''); ?></div>
            <div class="co-detail">
                <?php echo e($settings['company_address'] ?? ''); ?>, <?php echo e($settings['company_city'] ?? ''); ?>, <?php echo e($settings['company_state'] ?? ''); ?><br>
                Phone: <?php echo e($settings['company_phone'] ?? ''); ?> &nbsp;|&nbsp; <?php echo e($settings['company_email'] ?? ''); ?><br>
                GSTIN: <?php echo e($settings['company_gstin'] ?? ''); ?> &nbsp;|&nbsp; PAN: <?php echo e($settings['company_pan'] ?? ''); ?>

            </div>
        </div>
        <div>
            <div class="inv-label">Tax Invoice</div>
            <div class="inv-number"><?php echo e($invoice->number); ?></div>
            <div><span class="status-chip"><?php echo e(ucfirst($invoice->effective_status)); ?></span></div>
            <div class="dates">
                <div class="date-block">
                    <div class="dl">Invoice Date</div>
                    <div class="dv"><?php echo e($invoice->invoice_date->format('d M Y')); ?></div>
                </div>
                <?php if($invoice->due_date): ?>
                <div class="date-block">
                    <div class="dl">Due Date</div>
                    <div class="dv"><?php echo e($invoice->due_date->format('d M Y')); ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="parties">
        <div class="party">
            <div class="party-label">Bill To</div>
            <div class="party-name"><?php echo e($invoice->customer_name); ?></div>
            <div class="party-detail">
                <?php echo e($invoice->customer_billing_address); ?><br>
                <?php echo e($invoice->customer_city); ?>, <?php echo e($invoice->customer_state); ?>

            </div>
            <?php if($invoice->customer_gstin): ?>
                <div class="party-gstin">GSTIN: <?php echo e($invoice->customer_gstin); ?></div>
            <?php endif; ?>
        </div>
        <div class="party">
            <div class="party-label">Place of Supply</div>
            <div class="party-detail"><?php echo e($invoice->place_of_supply ?: $invoice->customer_state); ?></div>
            <div style="margin-top:8px;font-size:10px;font-weight:600;color:<?php echo e($invoice->is_intra_state ? '#0c7a59' : '#1a56db'); ?>;">
                <?php echo e($invoice->is_intra_state ? 'CGST + SGST Applicable' : 'IGST Applicable'); ?>

            </div>
        </div>
    </div>

    
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
            <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($i+1); ?></td>
                <td>
                    <strong><?php echo e($item->item_name); ?></strong>
                    <?php if($item->description): ?><br><small style="color:#777;"><?php echo e($item->description); ?></small><?php endif; ?>
                </td>
                <td><?php echo e($item->hsn_sac ?: '—'); ?></td>
                <td class="r"><?php echo e(rtrim(rtrim(number_format($item->qty,3),'0'),'.')); ?> <?php echo e($item->unit); ?></td>
                <td class="r"><?php echo e(fmt_inr($item->rate)); ?></td>
                <td class="r"><?php echo e($item->discount_percent > 0 ? $item->discount_percent.'%' : '—'); ?></td>
                <td class="r"><?php echo e(fmt_inr($item->taxable_amount)); ?></td>
                <td class="r"><?php echo e($item->gst_rate); ?>%</td>
                <td class="r"><?php echo e(fmt_inr($item->total_tax)); ?></td>
                <td class="r"><strong><?php echo e(fmt_inr($item->total_amount)); ?></strong></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    
    <div class="totals-wrap">
        <table class="totals-table">
            <tr><td class="tl">Subtotal</td><td class="tv"><?php echo e(fmt_inr($invoice->sub_total)); ?></td></tr>
            <?php if($invoice->discount_amount > 0): ?>
            <tr><td class="tl">Discount</td><td class="tv" style="color:#0c7a59;">-<?php echo e(fmt_inr($invoice->discount_amount)); ?></td></tr>
            <?php endif; ?>
            <tr>
                <td colspan="2">
                    <div class="gst-box">
                        <?php if($invoice->is_intra_state): ?>
                            <div class="gst-row"><span>CGST</span><span><?php echo e(fmt_inr($invoice->total_cgst)); ?></span></div>
                            <div class="gst-row"><span>SGST</span><span><?php echo e(fmt_inr($invoice->total_sgst)); ?></span></div>
                        <?php else: ?>
                            <div class="gst-row"><span>IGST</span><span><?php echo e(fmt_inr($invoice->total_igst)); ?></span></div>
                        <?php endif; ?>
                        <div class="gst-row" style="font-weight:600;border-top:1px solid #ddd;margin-top:4px;padding-top:4px;">
                            <span>Total Tax</span><span><?php echo e(fmt_inr($invoice->total_tax)); ?></span>
                        </div>
                    </div>
                </td>
            </tr>
            <?php if(abs($invoice->round_off) > 0): ?>
            <tr><td class="tl">Round Off</td><td class="tv"><?php echo e($invoice->round_off >= 0 ? '+' : ''); ?><?php echo e(fmt_inr($invoice->round_off)); ?></td></tr>
            <?php endif; ?>
            <tr class="grand"><td class="tl">Grand Total</td><td class="tv"><?php echo e(fmt_inr($invoice->grand_total)); ?></td></tr>
        </table>
    </div>
    <div class="words-box"><?php echo e(number_to_words($invoice->grand_total)); ?></div>

    
    <div class="footer-wrap">
        <div class="bank-box">
            <div class="bank-title">Bank Details</div>
            <div class="bank-detail">
                <?php echo e($settings['bank_name'] ?? ''); ?> &nbsp;|&nbsp; A/C: <?php echo e($settings['bank_acc_no'] ?? ''); ?><br>
                IFSC: <?php echo e($settings['bank_ifsc'] ?? ''); ?> &nbsp;|&nbsp; Branch: <?php echo e($settings['bank_branch'] ?? ''); ?>

            </div>
        </div>
        <div class="sign-box">
            <div class="sign-label">For <?php echo e($settings['company_name'] ?? ''); ?></div>
            <div class="sign-name">Authorised Signatory</div>
        </div>
    </div>

    <?php if($invoice->notes): ?>
    <div class="notes-box">
        <div class="notes-label">Notes</div>
        <div class="notes-text"><?php echo e($invoice->notes); ?></div>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/invoices/pdf.blade.php ENDPATH**/ ?>
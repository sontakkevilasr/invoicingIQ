<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-head flex justify-between items-center">
        <div>
            <div class="page-title">Dashboard</div>
            <div class="page-subtitle"><?php echo e(now()->format('l, d F Y')); ?></div>
        </div>
        <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary">+ New Invoice</a>
    </div>

    
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-label">Total Invoiced</div>
            <div class="stat-value"><?php echo e(fmt_inr($stats['total_invoiced'])); ?></div>
        </div>
        <div class="stat-card ok">
            <div class="stat-label">Paid</div>
            <div class="stat-value"><?php echo e(fmt_inr($stats['total_paid'])); ?></div>
        </div>
        <div class="stat-card acc">
            <div class="stat-label">Outstanding</div>
            <div class="stat-value"><?php echo e(fmt_inr($stats['total_outstanding'])); ?></div>
        </div>
        <div class="stat-card err">
            <div class="stat-label">Overdue</div>
            <div class="stat-value"><?php echo e(fmt_inr($stats['total_overdue'])); ?></div>
        </div>
    </div>

    
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:28px;">
        <div class="card" style="padding:16px 20px;">
            <div class="stat-label">Total Invoices</div>
            <div style="font-size:20px;font-weight:600;color:var(--t1);margin-top:4px;"><?php echo e($stats['invoice_count']); ?></div>
        </div>
        <div class="card" style="padding:16px 20px;">
            <div class="stat-label">Customers</div>
            <div style="font-size:20px;font-weight:600;color:var(--t1);margin-top:4px;"><?php echo e($stats['customer_count']); ?></div>
        </div>
        <div class="card" style="padding:16px 20px;">
            <div class="stat-label">Drafts Pending</div>
            <div style="font-size:20px;font-weight:600;color:var(--warn);margin-top:4px;"><?php echo e($stats['draft_count']); ?></div>
        </div>
    </div>

    
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Invoices</div>
            <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-sm btn-ghost">View All →</a>
        </div>
        <?php if($recent->isEmpty()): ?>
            <div style="padding:50px 20px;text-align:center;color:var(--t4);">
                No invoices yet.
                <a href="<?php echo e(route('invoices.create')); ?>" style="color:var(--accent);text-decoration:none;font-weight:500;"> Create your first invoice →</a>
            </div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Due Date</th>
                            <th class="r">Amount</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $eff = $inv->effective_status; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('invoices.edit', $inv)); ?>" style="color:var(--accent);font-weight:600;text-decoration:none;"><?php echo e($inv->number); ?></a>
                                </td>
                                <td><?php echo e($inv->customer_name ?: '—'); ?></td>
                                <td style="color:var(--t3);"><?php echo e($inv->invoice_date->format('d M Y')); ?></td>
                                <td style="color:<?php echo e($eff==='overdue' ? 'var(--err)' : 'var(--t3)'); ?>;">
                                    <?php echo e($inv->due_date ? $inv->due_date->format('d M Y') : '—'); ?>

                                </td>
                                <td class="r" style="font-weight:600;"><?php echo e(fmt_inr($inv->grand_total)); ?></td>
                                <td><span class="badge badge-<?php echo e($inv->status_color); ?>"><?php echo e(ucfirst($eff)); ?></span></td>
                                <td>
                                    <a href="<?php echo e(route('invoices.edit', $inv)); ?>" class="btn btn-xs">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/dashboard.blade.php ENDPATH**/ ?>
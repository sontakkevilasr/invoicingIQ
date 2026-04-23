<?php $__env->startSection('title', 'Invoices'); ?>

<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-head flex justify-between items-center">
        <div>
            <div class="page-title">Invoices</div>
            <div class="page-subtitle"><?php echo e($invoices->total()); ?> invoices total</div>
        </div>
        <a href="<?php echo e(route('invoices.create')); ?>" class="btn btn-primary">+ New Invoice</a>
    </div>

    
    <div class="toolbar">
        <form method="GET" style="display:flex;gap:8px;align-items:center;flex:1;">
            <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search by invoice # or customer…"
                class="form-control" style="max-width:300px;">
            <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control" style="width:150px;">
            <span style="color:var(--t4);font-size:12px;">to</span>
            <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control" style="width:150px;">
            <button type="submit" class="btn btn-sm">Filter</button>
            <?php if(request()->hasAny(['q','from','to','status'])): ?>
                <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-sm btn-ghost">Clear</a>
            <?php endif; ?>
        </form>
        <div class="filter-pills">
            <?php $__currentLoopData = ['all'=>'All','draft'=>'Draft','sent'=>'Sent','paid'=>'Paid','overdue'=>'Overdue','cancelled'=>'Cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('invoices.index', array_merge(request()->except('status','page'), ['status'=>$val]))); ?>"
                   class="filter-pill <?php echo e((request('status',$val==='all'?'':null)===$val || ($val==='all'&&!request('status'))) ? 'active' : ''); ?>">
                   <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="card">
        <?php if($invoices->isEmpty()): ?>
            <div style="padding:60px 20px;text-align:center;color:var(--t4);">No invoices found.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Invoice Date</th>
                            <th>Due Date</th>
                            <th class="r">Sub-total</th>
                            <th class="r">Grand Total</th>
                            <th>Status</th>
                            <th class="c">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $eff = $inv->effective_status; ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('invoices.edit', $inv)); ?>" style="color:var(--accent);font-weight:600;text-decoration:none;">
                                        <?php echo e($inv->number); ?>

                                    </a>
                                </td>
                                <td style="font-weight:500;"><?php echo e($inv->customer_name ?: '—'); ?></td>
                                <td style="color:var(--t3);"><?php echo e($inv->invoice_date->format('d M Y')); ?></td>
                                <td style="color:<?php echo e($eff==='overdue' ? 'var(--err)' : 'var(--t3)'); ?>;">
                                    <?php echo e($inv->due_date ? $inv->due_date->format('d M Y') : '—'); ?>

                                </td>
                                <td class="r"><?php echo e(fmt_inr($inv->sub_total)); ?></td>
                                <td class="r" style="font-weight:600;"><?php echo e(fmt_inr($inv->grand_total)); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($inv->status_color); ?>"><?php echo e(ucfirst($eff)); ?></span>
                                </td>
                                <td class="c">
                                    <div style="display:flex;gap:5px;justify-content:center;">
                                        <a href="<?php echo e(route('invoices.edit', $inv)); ?>" class="btn btn-xs">Edit</a>
                                        <a href="<?php echo e(route('invoices.pdf', $inv)); ?>" class="btn btn-xs" target="_blank">PDF</a>
                                        <form method="POST" action="<?php echo e(route('invoices.destroy', $inv)); ?>"
                                              onsubmit="return confirm('Delete <?php echo e($inv->number); ?>?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-xs btn-danger">Del</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div style="padding:12px 16px;">
                <?php echo e($invoices->links('partials.pagination')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/invoices/index.blade.php ENDPATH**/ ?>
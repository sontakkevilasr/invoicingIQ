<?php $__env->startSection('title','Settings'); ?>
<?php $__env->startSection('content'); ?>
<div class="page" style="max-width:760px;">
    <div class="page-head"><div class="page-title">Settings</div><div class="page-subtitle">Company profile &amp; billing preferences</div></div>
    <form method="POST" action="<?php echo e(route('settings.update')); ?>"><?php echo csrf_field(); ?>
        <div class="card" style="margin-bottom:16px;"><div class="card-header"><div class="card-title">Company Details</div></div><div class="card-body"><div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
            <?php $__currentLoopData = [['company_name','Company Name','text'],['company_gstin','GSTIN','text'],['company_pan','PAN','text'],['company_address','Address','text'],['company_city','City','text'],['company_state','State','text'],['company_state_code','State Code','text'],['company_phone','Phone','tel'],['company_email','Email','email']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$k,$l,$t]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group"><label class="form-label"><?php echo e($l); ?></label><input type="<?php echo e($t); ?>" name="<?php echo e($k); ?>" class="form-control" value="<?php echo e(old($k,$settings[$k]??'')); ?>"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div></div></div>
        <div class="card" style="margin-bottom:16px;"><div class="card-header"><div class="card-title">Bank Details</div></div><div class="card-body"><div style="display:grid;grid-template-columns:1fr 1fr;gap:0 16px;">
            <?php $__currentLoopData = [['bank_name','Bank Name'],['bank_acc_no','Account Number'],['bank_ifsc','IFSC Code'],['bank_branch','Branch']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$k,$l]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group"><label class="form-label"><?php echo e($l); ?></label><input type="text" name="<?php echo e($k); ?>" class="form-control" value="<?php echo e(old($k,$settings[$k]??'')); ?>"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div></div></div>
        <div class="card" style="margin-bottom:16px;"><div class="card-header"><div class="card-title">Invoice Settings</div></div><div class="card-body"><div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0 16px;">
            <?php $__currentLoopData = [['invoice_prefix','Invoice Prefix','text'],['invoice_seq','Next Sequence No.','number'],['default_terms','Payment Terms (days)','number']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$k,$l,$t]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="form-group"><label class="form-label"><?php echo e($l); ?></label><input type="<?php echo e($t); ?>" name="<?php echo e($k); ?>" class="form-control" value="<?php echo e(old($k,$settings[$k]??'')); ?>"></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div></div></div>
        <div class="card" style="margin-bottom:24px;"><div class="card-header"><div class="card-title">Default Invoice Notes</div></div><div class="card-body">
            <textarea name="default_notes" class="form-control" rows="3"><?php echo e(old('default_notes',$settings['default_notes']??'')); ?></textarea>
        </div></div>
        <button type="submit" class="btn btn-primary" style="padding:10px 28px;">Save Settings</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/settings/index.blade.php ENDPATH**/ ?>
<?php if($paginator->hasPages()): ?>
<div class="pagination">
    
    <?php if($paginator->onFirstPage()): ?>
        <span class="disabled"><span>‹</span></span>
    <?php else: ?>
        <a href="<?php echo e($paginator->previousPageUrl()); ?>">‹</a>
    <?php endif; ?>
    
    <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(is_string($element)): ?>
            <span><span><?php echo e($element); ?></span></span>
        <?php endif; ?>
        <?php if(is_array($element)): ?>
            <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($page == $paginator->currentPage()): ?>
                    <span class="active"><span><?php echo e($page); ?></span></span>
                <?php else: ?>
                    <a href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <?php if($paginator->hasMorePages()): ?>
        <a href="<?php echo e($paginator->nextPageUrl()); ?>">›</a>
    <?php else: ?>
        <span class="disabled"><span>›</span></span>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/partials/pagination.blade.php ENDPATH**/ ?>
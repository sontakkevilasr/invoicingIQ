<?php $__env->startSection('title','Items'); ?>
<?php $__env->startSection('content'); ?>
<div class="page">
    <div class="page-head flex justify-between items-center">
        <div><div class="page-title">Items &amp; Services</div><div class="page-subtitle"><?php echo e($items->total()); ?> items in master</div></div>
        <button class="btn btn-primary" onclick="openM()">+ Add Item</button>
    </div>
    <form method="GET" class="toolbar"><input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Search name, HSN…" class="form-control" style="max-width:320px;"><button class="btn btn-sm">Search</button><?php if(request('q')): ?><a href="<?php echo e(route('items.index')); ?>" class="btn btn-sm btn-ghost">Clear</a><?php endif; ?></form>
    <div class="card"><div class="table-wrap"><table class="tbl"><thead><tr><th>Item / Service</th><th>HSN/SAC</th><th>Default Rate</th><th>GST %</th><th>Unit</th><th>Type</th><th class="c">Actions</th></tr></thead><tbody>
    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <tr><td><div style="font-weight:500;"><?php echo e($item->name); ?></div><div style="font-size:11px;color:var(--t4);"><?php echo e($item->description); ?></div></td>
    <td style="font-family:monospace;font-size:11px;"><?php echo e($item->hsn_sac ?: '—'); ?></td>
    <td style="font-weight:500;font-variant-numeric:tabular-nums;"><?php echo e(fmt_inr($item->rate)); ?></td>
    <td><span class="badge badge-blue"><?php echo e($item->gst_rate); ?>%</span></td>
    <td style="color:var(--t3);"><?php echo e($item->unit); ?></td>
    <td><span class="badge <?php echo e($item->type==='goods'?'badge-green':'badge-gray'); ?>"><?php echo e(ucfirst($item->type)); ?></span></td>
    <td class="c"><div style="display:flex;gap:5px;justify-content:center;"><button class="btn btn-xs" onclick='openE(<?php echo json_encode($item, 15, 512) ?>)'>Edit</button><form method="POST" action="<?php echo e(route('items.destroy',$item)); ?>" onsubmit="return confirm('Delete?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-xs btn-danger">Del</button></form></div></td></tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr><td colspan="7" style="text-align:center;padding:50px;color:var(--t4);">No items yet.</td></tr>
    <?php endif; ?>
    </tbody></table></div><div style="padding:12px 16px;"><?php echo e($items->links('partials.pagination')); ?></div></div>
</div>
<div id="iModal" class="modal-overlay" style="display:none;"><div class="modal-box">
    <div class="modal-head"><div class="modal-title" id="mTitle">Add Item</div><button class="modal-close" onclick="closeM()">×</button></div>
    <form id="iForm" method="POST" action="<?php echo e(route('items.store')); ?>"><?php echo csrf_field(); ?><input type="hidden" id="fM" name="_method" value="">
    <div class="modal-body">
        <div class="form-group"><label class="form-label">Item / Service Name *</label><input type="text" name="name" id="fn" class="form-control" required></div>
        <div class="form-row form-row-2"><div class="form-group"><label class="form-label">Default Rate (₹) *</label><input type="number" name="rate" id="fr" class="form-control" min="0" step="0.01" required></div><div class="form-group"><label class="form-label">GST Rate *</label><select name="gst_rate" id="fg" class="form-control"><option value="0">0%</option><option value="5">5%</option><option value="12">12%</option><option value="18" selected>18%</option><option value="28">28%</option></select></div></div>
        <div class="form-row form-row-3"><div class="form-group"><label class="form-label">Unit</label><select name="unit" id="fu" class="form-control"><option>Nos</option><option>Pcs</option><option>Hrs</option><option>Kg</option><option>Ltr</option><option>Month</option><option>Year</option><option>Sqft</option></select></div><div class="form-group"><label class="form-label">HSN/SAC</label><input type="text" name="hsn_sac" id="fh" class="form-control"></div><div class="form-group"><label class="form-label">Type</label><select name="type" id="ft" class="form-control"><option value="service">Service</option><option value="goods">Goods</option></select></div></div>
        <div class="form-group"><label class="form-label">Description</label><input type="text" name="description" id="fd" class="form-control"></div>
    </div>
    <div class="modal-foot"><button type="button" class="btn" onclick="closeM()">Cancel</button><button class="btn btn-primary">Save Item</button></div></form>
</div></div>
<?php $__env->startPush('scripts'); ?><script>
function closeM(){document.getElementById('iModal').style.display='none';}
function openM(){document.getElementById('iModal').style.display='flex';document.getElementById('mTitle').textContent='Add Item';document.getElementById('iForm').action='<?php echo e(route('items.store')); ?>';document.getElementById('fM').value='';['fn','fr','fh','fd'].forEach(id=>{const e=document.getElementById(id);if(e)e.value='';});document.getElementById('fg').value='18';document.getElementById('fu').value='Nos';document.getElementById('ft').value='service';}
function openE(item){document.getElementById('iModal').style.display='flex';document.getElementById('mTitle').textContent='Edit Item';document.getElementById('iForm').action='/items/'+item.id;document.getElementById('fM').value='PUT';document.getElementById('fn').value=item.name||'';document.getElementById('fr').value=item.rate||0;document.getElementById('fg').value=item.gst_rate||18;document.getElementById('fu').value=item.unit||'Nos';document.getElementById('fh').value=item.hsn_sac||'';document.getElementById('ft').value=item.type||'service';document.getElementById('fd').value=item.description||'';}
document.getElementById('iModal').addEventListener('click',e=>{if(e.target===e.currentTarget)closeM();});
</script><?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/items/index.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'InvoiceIQ'); ?> — InvoiceIQ</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div class="app-shell">

    
    <aside class="sidebar no-print">
        <div class="sidebar-logo">
            <div class="brand">InvoiceIQ</div>
            <div class="tagline">GST Billing Suite</div>
        </div>
        <div class="sidebar-new-btn">
            <a href="<?php echo e(route('invoices.create')); ?>">
                <svg width="14" height="14" fill="none" viewBox="0 0 14 14"><circle cx="7" cy="7" r="6.5" stroke="currentColor" stroke-width="1.3"/><path d="M7 4v6M4 7h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                New Invoice
            </a>
        </div>
        <nav>
            <div class="nav-section">Main</div>
            <a href="<?php echo e(route('dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <svg viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="6" height="6" rx="1.5" fill="currentColor" opacity=".7"/><rect x="9" y="1" width="6" height="6" rx="1.5" fill="currentColor" opacity=".7"/><rect x="1" y="9" width="6" height="6" rx="1.5" fill="currentColor" opacity=".4"/><rect x="9" y="9" width="6" height="6" rx="1.5" fill="currentColor" opacity=".4"/></svg>
                Dashboard
            </a>
            <a href="<?php echo e(route('invoices.index')); ?>" class="nav-item <?php echo e(request()->routeIs('invoices.*') ? 'active' : ''); ?>">
                <svg viewBox="0 0 16 16" fill="none"><rect x="2" y="1" width="12" height="14" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M5 5h6M5 8h6M5 11h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                Invoices
            </a>
            <div class="nav-section">Masters</div>
            <a href="<?php echo e(route('customers.index')); ?>" class="nav-item <?php echo e(request()->routeIs('customers.*') ? 'active' : ''); ?>">
                <svg viewBox="0 0 16 16" fill="none"><circle cx="8" cy="5" r="3" stroke="currentColor" stroke-width="1.3"/><path d="M2 14c0-3.314 2.686-6 6-6s6 2.686 6 6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Customers
            </a>
            <a href="<?php echo e(route('items.index')); ?>" class="nav-item <?php echo e(request()->routeIs('items.*') ? 'active' : ''); ?>">
                <svg viewBox="0 0 16 16" fill="none"><rect x="1.5" y="1.5" width="5.5" height="5.5" rx="1.2" stroke="currentColor" stroke-width="1.3"/><rect x="9" y="1.5" width="5.5" height="5.5" rx="1.2" stroke="currentColor" stroke-width="1.3"/><rect x="1.5" y="9" width="5.5" height="5.5" rx="1.2" stroke="currentColor" stroke-width="1.3"/><path d="M11.75 9v5.5M9 11.75h5.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                Items
            </a>
            <div class="nav-section">Reports</div>
            <a href="<?php echo e(route('reports.gst')); ?>" class="nav-item <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                <svg viewBox="0 0 16 16" fill="none"><path d="M3 13V7M7 13V4M11 13V9M15 13H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                GST Reports
            </a>
            <div class="nav-section">Setup</div>
            <a href="<?php echo e(route('settings.index')); ?>" class="nav-item <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>">
                <svg viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="2.5" stroke="currentColor" stroke-width="1.3"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2M2.93 2.93l1.41 1.41M11.66 11.66l1.41 1.41M2.93 13.07l1.41-1.41M11.66 4.34l1.41-1.41" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                Settings
            </a>
        </nav>
    </aside>

    
    <main class="main">
        <?php if(session('success')): ?>
            <div style="padding: 0 28px; padding-top: 16px;">
                <div class="flash flash-success"><?php echo e(session('success')); ?></div>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div style="padding: 0 28px; padding-top: 16px;">
                <div class="flash flash-error"><?php echo e(session('error')); ?></div>
            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

</div>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/layouts/app.blade.php ENDPATH**/ ?>
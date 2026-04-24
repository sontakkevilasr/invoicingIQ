<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — InvoiceIQ</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
        }
        .login-wrap {
            width: 100%;
            max-width: 420px;
            padding: 24px;
        }
        .login-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .login-brand .brand-icon {
            width: 48px;
            height: 48px;
            background: var(--sidebar);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }
        .login-brand .brand-name {
            font-family: var(--font-head);
            font-size: 26px;
            color: var(--t1);
            line-height: 1;
        }
        .login-brand .brand-tagline {
            font-size: 10px;
            color: var(--t4);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 5px;
        }
        .login-card {
            background: var(--surface);
            border: 1px solid var(--bdr);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }
        .login-card-head {
            padding: 22px 28px 0;
        }
        .login-card-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--t1);
            margin-bottom: 4px;
        }
        .login-card-sub {
            font-size: 12px;
            color: var(--t4);
        }
        .login-card-body {
            padding: 20px 28px 28px;
        }
        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .remember-row input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: var(--accent);
            cursor: pointer;
        }
        .remember-row label {
            font-size: 12px;
            color: var(--t3);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-wrap">

        <div class="login-brand">
            <div class="brand-icon">
                <svg width="22" height="22" viewBox="0 0 16 16" fill="none">
                    <rect x="2" y="1" width="12" height="14" rx="2" stroke="white" stroke-width="1.3"/>
                    <path d="M5 5h6M5 8h6M5 11h4" stroke="white" stroke-width="1.2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="brand-name">InvoiceIQ</div>
            <div class="brand-tagline">GST Billing Suite</div>
        </div>

        <div class="login-card">
            <div class="login-card-head">
                <div class="login-card-title">Welcome back</div>
                <div class="login-card-sub">Sign in to your account to continue</div>
            </div>
            <div class="login-card-body">

                <?php if($errors->any()): ?>
                    <div class="flash flash-error" style="margin-bottom:16px;">
                        <?php echo e($errors->first()); ?>

                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="flash flash-error" style="margin-bottom:16px;">
                        <?php echo e(session('error')); ?>

                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('login.post')); ?>">
                    <?php echo csrf_field(); ?>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="<?php echo e(old('email')); ?>"
                            class="form-control <?php echo e($errors->has('email') ? 'error' : ''); ?>"
                            placeholder="you@company.com"
                            autofocus
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <div class="remember-row">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <label for="remember">Remember me for 30 days</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-full" style="justify-content:center;padding:10px 16px;">
                        Sign In
                    </button>
                </form>

            </div>
        </div>

        <p style="text-align:center;font-size:11px;color:var(--t4);margin-top:20px;">
            Contact your administrator if you need access.
        </p>

    </div>
</body>
</html>
<?php /**PATH D:\xampp\htdocs\invoiceiq\resources\views/auth/login.blade.php ENDPATH**/ ?>
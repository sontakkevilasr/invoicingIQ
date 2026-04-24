<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied — Trivo - InvoiceIQ</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--bg); }
    </style>
</head>
<body>
    <div style="text-align:center;max-width:420px;padding:32px;">
        <div style="font-size:56px;margin-bottom:16px;">🔒</div>
        <div style="font-family:var(--font-head);font-size:28px;color:var(--t1);margin-bottom:8px;">Access Denied</div>
        <div style="font-size:14px;color:var(--t3);margin-bottom:28px;line-height:1.6;">
            You don't have permission to access this page.<br>
            Contact your administrator if you think this is a mistake.
        </div>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
           class="btn btn-primary" style="margin-right:8px;">Go Back</a>
        <a href="{{ route('dashboard') }}" class="btn">Dashboard</a>
    </div>
</body>
</html>

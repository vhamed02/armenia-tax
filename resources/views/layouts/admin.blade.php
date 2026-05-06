<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} — ARFIMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @livewireStyles
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #f7f9fc; }
        .content-area { margin-left: 240px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 16px 28px; display: flex; align-items: center; justify-content: space-between; }
        .topbar-title { font-size: 20px; font-weight: 600; color: #1a202c; }
        .topbar-date { font-size: 13px; color: #718096; }
        .main-content { padding: 28px; flex: 1; }
        .card { background: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 14px; font-size: 12px; font-weight: 600; color: #718096; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px 14px; font-size: 14px; color: #2d3748; border-bottom: 1px solid #f0f4f8; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #f7f9fc; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-low { background: #c6f6d5; color: #276749; }
        .badge-medium { background: #fefcbf; color: #744210; }
        .badge-high { background: #fed7d7; color: #822727; }
        .badge-pending { background: #fefcbf; color: #744210; }
        .badge-submitted { background: #bee3f8; color: #2a4365; }
        .badge-acknowledged { background: #c6f6d5; color: #276749; }
        .btn { display: inline-block; padding: 6px 14px; border-radius: 5px; font-size: 13px; font-weight: 500; cursor: pointer; border: none; text-decoration: none; }
        .btn-primary { background: #1e88e5; color: #fff; }
        .btn-primary:hover { background: #1565c0; }
        .btn-success { background: #38a169; color: #fff; }
        .btn-sm { padding: 4px 10px; font-size: 12px; }
        .stat-card { background: #ffffff; border-radius: 8px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .stat-value { font-size: 28px; font-weight: 700; color: #1a202c; margin: 4px 0; }
        .stat-label { font-size: 13px; color: #718096; }
        .filter-btn { padding: 6px 16px; border-radius: 5px; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid #e2e8f0; background: #fff; color: #4a5568; }
        .filter-btn.active { background: #1e88e5; color: #fff; border-color: #1e88e5; }
        input[type=text], input[type=search] { border: 1px solid #e2e8f0; border-radius: 5px; padding: 8px 12px; font-size: 14px; outline: none; width: 100%; }
        input[type=text]:focus, input[type=search]:focus { border-color: #1e88e5; }
    </style>
</head>
<body>
    @include('layouts.partials.sidebar-admin')

    <div class="content-area">
        <div class="topbar">
            <div class="topbar-title">{{ $title ?? 'Dashboard' }}</div>
            <div class="topbar-date">{{ now()->format('l, F j, Y') }}</div>
        </div>
        <div class="main-content">
            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>

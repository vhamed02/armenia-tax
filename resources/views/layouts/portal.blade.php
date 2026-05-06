<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Portal' }} — ARFIMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
        .badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #fefcbf; color: #744210; }
        .badge-submitted { background: #bee3f8; color: #2a4365; }
        .badge-acknowledged { background: #c6f6d5; color: #276749; }
        .alert-danger { background: #fff5f5; border: 1px solid #fed7d7; border-radius: 8px; padding: 16px 20px; color: #822727; }
        .alert-success { background: #f0fff4; border: 1px solid #c6f6d5; border-radius: 8px; padding: 16px 20px; color: #276749; }
    </style>
</head>
<body>
    @include('layouts.partials.sidebar-portal')

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

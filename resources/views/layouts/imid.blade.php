<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>imID — Identity Verification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #1a3a2a; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }
        .spinner { width: 32px; height: 32px; border: 3px solid #00c85330; border-top-color: #00c853; border-radius: 50%; animation: spin 0.8s linear infinite; }
        .pulse { animation: pulse 2s ease-in-out infinite; }
    </style>
    @livewireScripts
</head>
<body>
    {{ $slot }}
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — ARFIMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body { margin: 0; background: #f7f9fc; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
    </style>
</head>
<body>
    <div style="background:#ffffff;border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.10);padding:40px 44px;width:100%;max-width:420px;">
        <div style="text-align:center;margin-bottom:32px;">
            <div style="display:inline-flex;align-items:center;justify-content:center;background:#0f1a2e;width:52px;height:52px;border-radius:10px;margin-bottom:14px;">
                <svg width="26" height="26" fill="none" stroke="#1e88e5" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div style="font-size:22px;font-weight:700;color:#1a202c;">ARFIMS</div>
            <div style="font-size:13px;color:#718096;margin-top:4px;">Armenian Revenue & Financial Intelligence</div>
        </div>

        @if($errors->any())
            <div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:6px;padding:12px 14px;margin-bottom:20px;color:#822727;font-size:13px;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:13px;font-weight:500;color:#4a5568;margin-bottom:6px;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;font-size:14px;outline:none;transition:border 0.2s;"
                    onfocus="this.style.borderColor='#1e88e5'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:13px;font-weight:500;color:#4a5568;margin-bottom:6px;">Password</label>
                <input type="password" name="password" required
                    style="width:100%;border:1px solid #e2e8f0;border-radius:6px;padding:10px 12px;font-size:14px;outline:none;transition:border 0.2s;"
                    onfocus="this.style.borderColor='#1e88e5'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <button type="submit"
                style="width:100%;background:#1e88e5;color:#ffffff;border:none;border-radius:6px;padding:11px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s;"
                onmouseover="this.style.background='#1565c0'" onmouseout="this.style.background='#1e88e5'">
                Sign In
            </button>
        </form>

        <div style="text-align:center;margin-top:20px;font-size:12px;color:#a0aec0;">
            State Revenue Committee of Armenia
        </div>
    </div>
</body>
</html>

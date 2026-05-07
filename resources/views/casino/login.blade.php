<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — VBet Demo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>* { font-family: 'Inter', sans-serif; box-sizing: border-box; } body { margin: 0; background: #0a0e1a; display: flex; align-items: center; justify-content: center; min-height: 100vh; }</style>
</head>
<body>
    <div style="width:100%;max-width:420px;padding:16px;">
        <div style="text-align:center;margin-bottom:28px;">
            <a href="{{ route('casino.home') }}" style="font-size:28px;font-weight:800;color:#00c853;text-decoration:none;letter-spacing:-1px;">VBet</a>
            <div style="font-size:12px;color:#8892a4;margin-top:4px;">DEMO PLATFORM</div>
        </div>

        <div style="background:#111827;border:1px solid #1e2d45;border-radius:12px;padding:32px;">
            <div style="font-size:20px;font-weight:700;color:#fff;margin-bottom:24px;">Sign In</div>

            @if($errors->any())
                <div style="background:#f4433620;border:1px solid #f4433640;border-radius:8px;padding:12px;margin-bottom:16px;color:#f44336;font-size:13px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('casino.login.post') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;color:#8892a4;margin-bottom:6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        style="width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;color:#fff;font-size:14px;outline:none;"
                        onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                </div>
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:13px;color:#8892a4;margin-bottom:6px;">Password</label>
                    <input type="password" name="password" required
                        style="width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;color:#fff;font-size:14px;outline:none;"
                        onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                </div>
                <button type="submit" style="width:100%;background:#00c853;color:#000;border:none;border-radius:8px;padding:12px;font-size:15px;font-weight:700;cursor:pointer;">
                    Sign In
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;font-size:13px;color:#8892a4;">
                No account? <a href="{{ route('casino.register') }}" style="color:#00c853;text-decoration:none;">Register</a>
            </div>
        </div>
    </div>
</body>
</html>

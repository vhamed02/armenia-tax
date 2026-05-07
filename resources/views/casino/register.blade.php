<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — VBet Demo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>* { font-family: 'Inter', sans-serif; box-sizing: border-box; } body { margin: 0; background: #0a0e1a; display: flex; align-items: center; justify-content: center; min-height: 100vh; }</style>
</head>
<body>
    <div style="width:100%;max-width:440px;padding:16px;">
        <div style="text-align:center;margin-bottom:28px;">
            <a href="{{ route('casino.home') }}" style="font-size:28px;font-weight:800;color:#00c853;text-decoration:none;letter-spacing:-1px;">VBet</a>
            <div style="font-size:12px;color:#8892a4;margin-top:4px;">DEMO PLATFORM</div>
        </div>

        <div style="background:#111827;border:1px solid #1e2d45;border-radius:12px;padding:32px;">
            <div style="font-size:20px;font-weight:700;color:#fff;margin-bottom:24px;">Create Account</div>

            @if($errors->any())
                <div style="background:#f4433620;border:1px solid #f4433640;border-radius:8px;padding:12px;margin-bottom:16px;color:#f44336;font-size:13px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('casino.register.post') }}">
                @csrf
                @foreach([['name','Full Name','text'],['email','Email Address','email'],['phone','Phone Number','text']] as [$field,$label,$type])
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:13px;color:#8892a4;margin-bottom:6px;">{{ $label }}</label>
                    <input type="{{ $type }}" name="{{ $field }}" value="{{ old($field) }}" {{ $field !== 'phone' ? 'required' : '' }}
                        style="width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;color:#fff;font-size:14px;outline:none;"
                        onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                </div>
                @endforeach
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:13px;color:#8892a4;margin-bottom:6px;">Password</label>
                    <input type="password" name="password" required
                        style="width:100%;background:#0a0e1a;border:1px solid #1e2d45;border-radius:8px;padding:10px 14px;color:#fff;font-size:14px;outline:none;"
                        onfocus="this.style.borderColor='#00c853'" onblur="this.style.borderColor='#1e2d45'">
                </div>
                <button type="submit" style="width:100%;background:#00c853;color:#000;border:none;border-radius:8px;padding:12px;font-size:15px;font-weight:700;cursor:pointer;">
                    Create Account
                </button>
            </form>

            <div style="text-align:center;margin-top:20px;font-size:13px;color:#8892a4;">
                Already have an account? <a href="{{ route('casino.login') }}" style="color:#00c853;text-decoration:none;">Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>

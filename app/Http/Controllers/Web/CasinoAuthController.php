<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CasinoAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('casino.home');
        }
        return view('casino.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, false)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        $request->session()->regenerate();

        $this->ensureCasinoProfile(Auth::user());

        return redirect()->route('casino.home');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('casino.home');
        }
        return view('casino.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'phone'    => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'        => $data['name'],
            'national_id' => $this->generateNationalId(),
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'password'    => Hash::make($data['password']),
            'is_admin'    => false,
        ]);

        $this->ensureCasinoProfile($user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('casino.home');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('casino.login');
    }

    private function ensureCasinoProfile(User $user): void
    {
        $provider = ServiceProvider::where('slug', 'softconstruct')->first();
        if (!$provider) {
            return;
        }

        $user->casinoProfiles()->firstOrCreate(
            ['service_provider_id' => $provider->id],
            [
                'wallet_balance'  => 0,
                'kyc_status'      => 'not_started',
                'casino_username' => explode('@', $user->email)[0],
            ]
        );
    }

    private function generateNationalId(): string
    {
        do {
            $id = str_pad(random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        } while (User::where('national_id', $id)->exists());

        return $id;
    }
}

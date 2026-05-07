<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CasinoKycController extends Controller
{
    public function start(Request $request): RedirectResponse
    {
        $user     = Auth::user();
        $provider = ServiceProvider::where('slug', 'softconstruct')->firstOrFail();

        $profile = $user->casinoProfiles()
            ->where('service_provider_id', $provider->id)
            ->firstOrFail();

        $token = Str::uuid()->toString();

        $profile->update([
            'kyc_status'        => 'in_progress',
            'kyc_session_token' => $token,
        ]);

        return redirect('/mock-imid/verify?session=' . $token . '&callback=' . urlencode('/casino/kyc/callback'));
    }

    public function callback(Request $request): RedirectResponse
    {
        $session = $request->query('session');
        $status  = $request->query('status', 'failed');

        $provider = ServiceProvider::where('slug', 'softconstruct')->firstOrFail();
        $user     = Auth::user();

        $profile = $user->casinoProfiles()
            ->where('service_provider_id', $provider->id)
            ->where('kyc_session_token', $session)
            ->first();

        if (!$profile) {
            return redirect()->route('casino.account')->with('error', 'Invalid or expired KYC session.');
        }

        if ($status === 'success') {
            $profile->update([
                'kyc_status'          => 'verified',
                'kyc_verified_at'     => now(),
                'national_id_verified'=> $user->national_id,
                'kyc_session_token'   => null,
            ]);

            $user->notifications()->create([
                'type'    => 'report_generated',
                'title'   => 'Identity Verified',
                'message' => 'Your imID verification was successful. Your wallet is now active.',
            ]);

            return redirect()->route('casino.account')->with('success', 'Identity verified successfully!');
        }

        $profile->update([
            'kyc_status'        => 'failed',
            'kyc_session_token' => null,
        ]);

        return redirect()->route('casino.account')->with('error', 'Identity verification failed. Please try again.');
    }
}

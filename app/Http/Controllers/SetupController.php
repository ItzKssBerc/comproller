<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function create(Request $request)
    {
        if (User::exists() && !$request->session()->has('setup_user_id')) {
            return redirect('/admin');
        }

        if ($request->session()->has('setup_user_id')) {
            $user = User::findOrFail($request->session()->get('setup_user_id'));

            $google2fa = app('pragmarx.google2fa');
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                $user->name . ' Comprollere',
                $user->email,
                $user->google2fa_secret
            );

            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(250),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer = new \BaconQrCode\Writer($renderer);
            $qrCodeSvg = $writer->writeString($qrCodeUrl);

            return view('setup', [
                'qrCodeSvg' => $qrCodeSvg,
                'secret' => $user->google2fa_secret,
                'show2Fa' => true
            ]);
        }

        return view('setup', ['show2Fa' => false]);
    }

    public function store(Request $request)
    {
        if (User::exists() && !$request->session()->has('setup_user_id')) {
            return redirect('/admin');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $google2fa = app('pragmarx.google2fa');

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => UserRole::App,
            'google2fa_secret' => $google2fa->generateSecretKey(),
        ]);

        $request->session()->put('setup_user_id', $user->id);

        return redirect()->route('setup');
    }

    public function verify2fa(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        if (!$request->session()->has('setup_user_id')) {
            return redirect()->route('setup');
        }

        $user = User::findOrFail($request->session()->get('setup_user_id'));

        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            $request->session()->forget('setup_user_id');
            Auth::login($user);
            return redirect('/admin');
        }

        return back()->withErrors(['one_time_password' => __('Invalid 2FA code.')]);
    }
}

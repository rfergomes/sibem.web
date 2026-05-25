<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            
            // Put active tenant in session
            session()->put('active_admlc_id', Auth::user()->local_id);
            session()->flash('show_login_toast', true);

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas estão incorretas ou a conta está inativa.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:mysql_sys.users_v2,email']);

        $user = User::where('email', $request->email)->first();
        
        // Generate recovery token and save to remember_token column (recovery password on users table)
        $token = Str::random(60);
        $user->forceFill([
            'remember_token' => $token,
        ])->save();

        // Send recovery email with link to reset password
        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);
        
        try {
            Mail::raw("Olá {$user->nome},\n\nPara redefinir sua senha, acesse o seguinte link:\n{$resetLink}\n\nSe você não solicitou isso, desconsidere este e-mail.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Recuperação de Senha - SIBEM');
            });
        } catch (\Exception $e) {
            // Fallback if mailer is not configured yet
            logger()->info("E-mail de recuperação para {$user->email}: Link -> {$resetLink}");
        }

        return back()->with('success', 'Se o e-mail estiver cadastrado, enviamos um link para redefinição de senha.');
    }

    public function showResetPassword($token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:mysql_sys.users_v2,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('remember_token', $request->token)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Token de recuperação inválido ou expirado.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => null,
        ])->save();

        return redirect()->route('login')->with('success', 'Senha redefinida com sucesso. Faça login com a nova senha.');
    }
}

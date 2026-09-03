<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditoriaLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function formulario()
    {
        return view('auth.passwords.email');
    }

    public function enviarEnlace(Request $request)
    {
        $request->validate(['correo' => 'required|email|exists:users,email'], [
            'correo.exists' => 'Este correo no está registrado en el sistema.'
        ]);

        $usuario = User::where('email', $request->correo)->first();
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $usuario->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $url = route('password.reset', $token) . '?correo=' . urlencode($usuario->email);

        Mail::raw("Hola {$usuario->nombre_completo},\n\nRestablece tu contraseña en este enlace (válido por 1 hora):\n{$url}\n\nSi no solicitaste esto, ignora este correo.",
            function ($m) use ($usuario) {
                $m->to($usuario->email)->subject('Recuperar contraseña - Tienda Luxor');
            });

        AuditoriaLog::registrar('Restablecer', 'Usuarios', "Enlace de recuperación enviado a {$usuario->email}");

        return back()->with('success', 'Te enviamos un correo con el enlace de recuperación.');
    }

    public function formularioReset($token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'correo' => request('correo'),
        ]);
    }

    public function restablecer(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $registro = DB::table('password_reset_tokens')->where('email', $request->correo)->first();

        if (!$registro || !Hash::check($request->token, $registro->token)) {
            return back()->withErrors(['correo' => 'Enlace inválido o ya utilizado.']);
        }

        if (now()->subMinutes(60)->greaterThan($registro->created_at)) {
            return back()->withErrors(['correo' => 'El enlace expiró (1 hora de validez).']);
        }

        User::where('email', $request->correo)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->correo)->delete();

        AuditoriaLog::registrar('Restablecer', 'Usuarios', "Contraseña restablecida por correo para {$request->correo}");

        return redirect()->route('login')->with('success', 'Contraseña actualizada. Ya puedes ingresar.');
    }
}
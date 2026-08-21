<?php

namespace App\Http\Controllers;

use App\Models\AuditoriaLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usuario_login' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt(['usuario_login' => $request->usuario_login, 'password' => $request->password], $request->remember)) {
            AuditoriaLog::registrar('Login', 'Autenticación', 'Inicio de sesión exitoso'); // 👇 AQUÍ
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['usuario_login' => 'Credenciales incorrectas']);
    }

    public function logout()
    {
        AuditoriaLog::registrar('Logout', 'Autenticación', 'Cierre de sesión'); // 👇 ANTES de logout (después ya no hay usuario)
        Auth::logout();
        return redirect()->route('login');
    }
        public function perfil()
    {
        return view('auth.perfil', ['usuario' => Auth::user()]);
    }

    public function updatePerfil(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $usuario = Auth::user();

        if (!Hash::check($request->password_actual, $usuario->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }

        if ($request->filled('nombre_completo')) {
            $usuario->nombre_completo = $request->nombre_completo;
        }
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        AuditoriaLog::registrar('Editar', 'Autenticación', 'Actualizó su perfil');

        return redirect()->route('perfil')->with('success', 'Perfil actualizado correctamente');
    }
}
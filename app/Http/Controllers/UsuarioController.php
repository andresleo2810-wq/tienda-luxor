<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\AuditoriaLog;

class UsuarioController extends Controller
{
    /**
     * Listar usuarios
     */
    public function index(Request $request)
    {
        $query = User::with('rol');
        
        // Búsqueda por nombre o usuario
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_completo', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('usuario_login', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('email', 'LIKE', "%{$request->buscar}%");
            });
        }
        
        // Filtro por rol
        if ($request->filled('rol')) {
            $query->where('id_rol', $request->rol);
        }
        
        $usuarios = $query->orderBy('nombre_completo')->paginate(10)->withQueryString();
        $roles = Rol::all();
        
        return view('usuarios.index', compact('usuarios', 'roles'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'usuario_login' => 'required|string|max:50|unique:users,usuario_login',
            'password' => 'required|string|min:6|confirmed',
            'id_rol' => 'required|exists:roles,id',
            'estado' => 'nullable|boolean'
        ], [
            'nombre_completo.required' => 'El nombre completo es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.unique' => 'Este email ya está registrado',
            'usuario_login.required' => 'El usuario de login es obligatorio',
            'usuario_login.unique' => 'Este usuario ya existe',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'id_rol.required' => 'Debe asignar un rol al usuario'
        ]);
        
        User::create([
            'nombre_completo' => $validated['nombre_completo'],
            'email' => $validated['email'],
            'usuario_login' => $validated['usuario_login'],
            'password' => Hash::make($validated['password']),
            'id_rol' => $validated['id_rol'],
            'estado' => $request->boolean('estado', true)
        ]);
        AuditoriaLog::registrar('Crear', 'Usuarios', "Usuario: {$validated['usuario_login']}");
        
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $roles = Rol::all();
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);
        
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'usuario_login' => 'required|string|max:50|unique:users,usuario_login,' . $usuario->id,
            'password' => 'nullable|string|min:6|confirmed',
            'id_rol' => 'required|exists:roles,id',
            'estado' => 'nullable|boolean'
        ], [
            'email.unique' => 'Este email ya está registrado',
            'usuario_login.unique' => 'Este usuario ya existe',
            'password.confirmed' => 'Las contraseñas no coinciden'
        ]);
        
        // Actualizar datos básicos
        $usuario->nombre_completo = $validated['nombre_completo'];
        $usuario->email = $validated['email'];
        $usuario->usuario_login = $validated['usuario_login'];
        $usuario->id_rol = $validated['id_rol'];
        $usuario->estado = $request->boolean('estado', true);
        
        // Solo actualizar contraseña si se ingresó una nueva
        if ($request->filled('password')) {
            $usuario->password = Hash::make($validated['password']);
        }
        
        $usuario->save();
        AuditoriaLog::registrar('Editar', 'Usuarios', "Usuario: {$usuario->usuario_login}");
        
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Eliminar usuario (soft delete)
     */
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        
        // No permitir eliminar al usuario actual
        if ($usuario->id === auth()->id()) {
            return redirect()->route('usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario');
        }
        
        // Soft delete: cambiar estado
        $usuario->update(['estado' => false]);
        AuditoriaLog::registrar('Eliminar', 'Usuarios', "Usuario: {$usuario->usuario_login}");
        
        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }
}
<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nombre_completo' => 'Administrador Principal',
            'email' => 'admin@tiendaluxor.com',
            'usuario_login' => 'admin',
            'password' => Hash::make('admin123'),
            'id_rol' => 1,
            'estado' => true
        ]);
        
        User::create([
            'nombre_completo' => 'Cajero de Prueba',
            'email' => 'cajero@tiendaluxor.com',
            'usuario_login' => 'cajero',
            'password' => Hash::make('cajero123'),
            'id_rol' => 2,
            'estado' => true
        ]);
    }
}
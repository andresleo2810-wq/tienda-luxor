<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;

class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rol::create([
            'nombre_rol' => 'Administrador',
            'descripcion' => 'Acceso completo al sistema'
        ]);

        Rol::create([
            'nombre_rol' => 'Cajero',
            'descripcion' => 'Solo puede registrar ventas y consultar'
        ]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'nit', 'contacto', 'telefono', 
        'email', 'direccion', 'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_proveedor');
    }
}
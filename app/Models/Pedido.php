<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';

    protected $fillable = [
        'id_proveedor', 'id_usuario', 'total_pedido',
        'estado', 'fecha_pedido', 'observaciones',
        'ruta_factura', 'fecha_recepcion', 'texto_ocr'
    ];

    protected $casts = [
        'total_pedido' => 'decimal:2',
        'fecha_pedido' => 'date',
        'fecha_recepcion' => 'datetime'
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'id_pedido');
    }
}
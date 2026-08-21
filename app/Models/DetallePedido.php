<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedidos';

    protected $fillable = [
        'id_pedido', 'id_producto', 'cantidad', 
        'costo_unitario', 'subtotal', 'cantidad_recibida'   // ← AGREGAR
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
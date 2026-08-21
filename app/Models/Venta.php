<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    
    protected $fillable = [
        'id_usuario',
        'total_venta',
        'metodo_pago',
        'estado'
    ];
    
    protected $casts = [
        'total_venta' => 'decimal:2'
    ];
    
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
    
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }
}
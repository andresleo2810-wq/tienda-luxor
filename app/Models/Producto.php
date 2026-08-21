<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'nombre_producto', 'marca', 'descripcion', 'codigo_barras',
        'precio_costo', 'precio_venta', 'stock_actual', 'stock_minimo',
        'categoria', 'grado_alcoholico', 'volumen_ml', 'pais_origen',
        'fecha_vencimiento', 'estado'
    ];

    protected $casts = [
        'precio_costo' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'grado_alcoholico' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'estado' => 'boolean'
    ];

    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';

    protected $fillable = [
        'id_usuario', 'monto_inicial', 'fecha_apertura', 'fecha_cierre',
        'monto_final_cierre', 'monto_esperado', 'diferencia', 'estado', 'notas'
    ];

    protected $casts = [
        'monto_inicial' => 'decimal:2',
        'monto_final_cierre' => 'decimal:2',
        'monto_esperado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
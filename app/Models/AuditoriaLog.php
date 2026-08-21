<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaLog extends Model
{
    protected $table = 'auditoria_logs';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario', 'usuario_nombre', 'accion', 
        'modulo', 'descripcion', 'ip_address', 'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

  
    public static function registrar($accion, $modulo, $descripcion)
    {
        static::create([
            'id_usuario' => auth()->id(),
            'usuario_nombre' => auth()->user() ? auth()->user()->nombre_completo : 'Sistema',
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';  // ← AGREGA ESTO EXPLÍCITAMENTE
    
    protected $fillable = [
        'nombre_rol',
        'descripcion'
    ];
    
    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol');
    }
}
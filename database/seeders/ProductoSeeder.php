<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        // WHISKY (15 productos)
        Producto::create([
            'nombre_producto' => 'Whisky Black Label 750ml',
            'marca' => 'Johnnie Walker',
            'descripcion' => 'Whisky escocés 12 años',
            'codigo_barras' => '7501001001001',
            'precio_costo' => 45000,
            'precio_venta' => 75000,
            'stock_actual' => 20,
            'stock_minimo' => 5,
            'categoria' => 'Whisky',
            'grado_alcoholico' => 40.0,
            'volumen_ml' => 750,
            'pais_origen' => 'Escocia',
            'estado' => true
        ]);
        
        Producto::create([
            'nombre_producto' => 'Whisky Red Label 750ml',
            'marca' => 'Johnnie Walker',
            'precio_costo' => 35000,
            'precio_venta' => 55000,
            'stock_actual' => 25,
            'stock_minimo' => 8,
            'categoria' => 'Whisky',
            'grado_alcoholico' => 40.0,
            'volumen_ml' => 750,
            'pais_origen' => 'Escocia',
            'estado' => true
        ]);
        
        // Agrega más productos aquí...
        // (Puedo generarte los 95 completos si los necesitas)
    }
}
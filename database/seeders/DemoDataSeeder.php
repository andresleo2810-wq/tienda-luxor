<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // ============ PROVEEDORES ============
        $provIds = [];
        $proveedores = [
            ['nombre' => 'Distribuidora de Licores S.A.S', 'nit' => '900.123.456-7', 'telefono' => '604 123 4567', 'email' => 'ventas@dilicores.com', 'direccion' => 'Cra 45 # 12-34, Medellín', 'estado' => true],
            ['nombre' => 'Bavaria S.A.', 'nit' => '890.456.123-1', 'telefono' => '601 789 4561', 'email' => 'pedidos@bavaria.com', 'direccion' => 'Autopista Norte # 100-20, Bogotá', 'estado' => true],
            ['nombre' => 'Vinos y Licores del Valle', 'nit' => '901.789.456-2', 'telefono' => '602 456 1237', 'email' => 'contacto@vinosvalle.com', 'direccion' => 'Av 3 # 25-10, Cali', 'estado' => true],
        ];
        foreach ($proveedores as $p) {
            $p['created_at'] = $now; $p['updated_at'] = $now;
            $provIds[] = DB::table('proveedores')->insertGetId($p);
        }

        // ============ PRODUCTOS (63) ============
        $productos = [
            ['Whisky Johnnie Walker Red Label 750ml', 'Johnnie Walker', 'Whisky', 38000, 55000, 29, 10, 40, 750, 'Escocia', null],
            ['Whisky Johnnie Walker Black Label 750ml', 'Johnnie Walker', 'Whisky', 52000, 75000, 90, 10, 40, 750, 'Escocia', null],
            ['Whisky Johnnie Walker Double Black 750ml', 'Johnnie Walker', 'Whisky', 68000, 95000, 12, 4, 40, 750, 'Escocia', null],
            ['Whisky Johnnie Walker Gold Label 750ml', 'Johnnie Walker', 'Whisky', 95000, 135000, 8, 3, 40, 750, 'Escocia', null],
            ['Whisky Johnnie Walker Blue Label 750ml', 'Johnnie Walker', 'Whisky', 550000, 780000, 3, 2, 40, 750, 'Escocia', null],
            ['Whisky Buchanans Master 750ml', 'Buchanans', 'Whisky', 150000, 210000, 9, 3, 40, 750, 'Escocia', null],
            ['Whisky Buchanan’s 12 años 750ml', 'Buchanans', 'Whisky', 180000, 250000, 6, 3, 40, 750, 'Escocia', null],
            ['Whisky Chivas Regal 12 años 750ml', 'Chivas', 'Whisky', 95000, 135000, 14, 5, 40, 750, 'Escocia', null],
            ['Whisky Chivas Regal 18 años 750ml', 'Chivas', 'Whisky', 220000, 310000, 4, 2, 40, 750, 'Escocia', null],
            ['Whisky Old Parr 12 años 750ml', 'Old Parr', 'Whisky', 85000, 120000, 16, 5, 40, 750, 'Escocia', null],
            ['Whisky Grants 750ml', 'Grants', 'Whisky', 45000, 65000, 20, 6, 40, 750, 'Escocia', null],
            ['Whisky Something Special 750ml', 'Something Special', 'Whisky', 60000, 85000, 15, 5, 40, 750, 'Escocia', null],
            ['Ron Medellín 8 años 750ml', 'Ron Medellín', 'Ron', 40000, 57900, 11, 5, 38, 750, 'Colombia', null],
            ['Ron Viejo de Caldas 750ml', 'Viejo de Caldas', 'Ron', 30000, 45000, 26, 8, 38, 750, 'Colombia', null],
            ['Ron Santa Fe 750ml', 'Santa Fe', 'Ron', 28000, 42000, 18, 6, 38, 750, 'Colombia', null],
            ['Ron Cartavio 5 años 750ml', 'Cartavio', 'Ron', 35000, 50000, 14, 5, 38, 750, 'Colombia', null],
            ['Ron Havanna Club 7 años 750ml', 'Havanna Club', 'Ron', 70000, 100000, 10, 4, 40, 750, 'Cuba', null],
            ['Ron Zacapa 23 años 750ml', 'Zacapa', 'Ron', 220000, 320000, 4, 2, 40, 750, 'Guatemala', null],
            ['Ron Bacardí Carta Blanca 750ml', 'Bacardí', 'Ron', 40000, 58000, 12, 5, 38, 750, 'Puerto Rico', null],
            ['Aguardiente Antioqueño 750ml', 'Antioqueño', 'Aguardiente', 22000, 31500, 8, 5, 29, 750, 'Colombia', null],
            ['Aguardiente Antioqueño Tapa Azul 375ml', 'Antioqueño', 'Aguardiente', 14000, 20000, 30, 10, 29, 375, 'Colombia', null],
            ['Aguardiente Amarillo de Manzanares 750ml', 'Manzanares', 'Aguardiente', 24000, 34000, 25, 8, 29, 750, 'Colombia', null],
            ['Aguardiente Blanco 750ml', 'Blanco', 'Aguardiente', 20000, 29000, 12, 5, 29, 750, 'Colombia', null],
            ['Aguardiente Caucano 750ml', 'Caucano', 'Aguardiente', 22000, 32000, 10, 5, 29, 750, 'Colombia', null],
            ['Aguardiente Néctar 750ml', 'Néctar', 'Aguardiente', 23000, 33000, 16, 6, 29, 750, 'Colombia', null],
            ['Vodka Smirnoff 750ml', 'Smirnoff', 'Vodka', 28000, 42000, 22, 6, 37, 750, 'Rusia', null],
            ['Vodka Absolut 750ml', 'Absolut', 'Vodka', 45000, 65000, 18, 6, 40, 750, 'Suecia', null],
            ['Vodka Grey Goose 750ml', 'Grey Goose', 'Vodka', 110000, 160000, 6, 3, 40, 750, 'Francia', null],
            ['Vodka Skyy 750ml', 'Skyy', 'Vodka', 38000, 55000, 12, 5, 40, 750, 'USA', null],
            ['Tequila Don Julio Blanco 750ml', 'Don Julio', 'Tequila', 78000, 112000, 15, 5, 40, 750, 'México', null],
            ['Tequila José Cuervo Especial 750ml', 'José Cuervo', 'Tequila', 60000, 88000, 14, 5, 38, 750, 'México', null],
            ['Tequila 1800 Reposado 750ml', '1800', 'Tequila', 85000, 125000, 8, 4, 38, 750, 'México', null],
            ['Tequila Patrón Silver 750ml', 'Patrón', 'Tequila', 160000, 230000, 5, 2, 40, 750, 'México', null],
            ['Ginebra Bombay Sapphire 750ml', 'Bombay', 'Gin', 85000, 118000, 12, 4, 47, 750, 'Inglaterra', null],
            ['Ginebra Tanqueray 750ml', 'Tanqueray', 'Gin', 90000, 130000, 9, 4, 47, 750, 'Inglaterra', null],
            ['Ginebra Larios 750ml', 'Larios', 'Gin', 45000, 65000, 10, 4, 40, 750, 'España', null],
            ['Brandy Fundador 750ml', 'Fundador', 'Brandy', 55000, 80000, 10, 4, 40, 750, 'España', null],
            ['Brandy Domecq 750ml', 'Domecq', 'Brandy', 48000, 70000, 8, 4, 40, 750, 'España', null],
            ['Crema de Whisky Baileys 750ml', 'Baileys', 'Cremosos', 60000, 88000, 14, 5, 17, 750, 'Irlanda', 180],
            ['Amaretto Disaronno 750ml', 'Disaronno', 'Cremosos', 65000, 95000, 7, 3, 28, 750, 'Italia', null],
            ['Anís del Mono 750ml', 'Del Mono', 'Anisados', 50000, 72000, 6, 3, 40, 750, 'España', null],
            ['Cerveza Águila x6', 'Bavaria', 'Cervezas', 12000, 17000, 8, 12, 4, 1980, 'Colombia', 45],
            ['Cerveza Club Colombia x6', 'Bavaria', 'Cervezas', 13000, 18500, 42, 10, 5, 1980, 'Colombia', 60],
            ['Cerveza Poker x6', 'Bavaria', 'Cervezas', 12000, 17000, 30, 10, 4, 1980, 'Colombia', 50],
            ['Cerveza Pilsen x6', 'Bavaria', 'Cervezas', 11000, 16000, 25, 10, 4, 1980, 'Colombia', 55],
            ['Cerveza Costeñita x6', 'Bavaria', 'Cervezas', 11500, 16500, 20, 8, 4, 1980, 'Colombia', 10],
            ['Cerveza Heineken x6', 'Heineken', 'Cervezas', 18000, 26000, 15, 6, 5, 1980, 'Holanda', 90],
            ['Cerveza Corona x6', 'Corona', 'Cervezas', 20000, 29000, 12, 6, 4, 2100, 'México', 90],
            ['Cerveza Stella Artois x6', 'Stella Artois', 'Cervezas', 19000, 27000, 10, 5, 5, 1980, 'Bélgica', 90],
            ['Vino Casillero del Diablo', 'Concha y Toro', 'Vinos', 21000, 29900, 4, 6, 13, 750, 'Chile', 4],
            ['Vino Santa Helena 750ml', 'Santa Helena', 'Vinos', 25000, 36000, 12, 5, 13, 750, 'Chile', 365],
            ['Vino Gato Negro 750ml', 'Gato Negro', 'Vinos', 28000, 40000, 14, 5, 13, 750, 'Chile', 300],
            ['Vino Marqués de Cáceres', 'Marqués de Cáceres', 'Vinos', 45000, 65000, 8, 4, 13, 750, 'España', 365],
            ['Vino San Pedro Reservado 750ml', 'San Pedro', 'Vinos', 22000, 32000, 10, 5, 12, 750, 'Chile', 300],
            ['Champagne Moët & Chandon 750ml', 'Moët', 'Espumosos', 220000, 320000, 4, 2, 12, 750, 'Francia', 540],
            ['Espumoso André 750ml', 'André', 'Espumosos', 30000, 45000, 12, 5, 11, 750, 'USA', 400],
            ['Champaña Santa Helena', 'Santa Helena', 'Espumosos', 35000, 50000, 10, 5, 11, 750, 'Colombia', 400],
           
        ];
        $prodIds = [];
        foreach ($productos as $p) {
            $prodIds[] = DB::table('productos')->insertGetId([
                'nombre_producto' => $p[0], 'marca' => $p[1], 'categoria' => $p[2],
                'precio_costo' => $p[3], 'precio_venta' => $p[4],
                'stock_actual' => $p[5], 'stock_minimo' => $p[6],
                'grado_alcoholico' => $p[7], 'volumen_ml' => $p[8], 'pais_origen' => $p[9],
                'fecha_vencimiento' => $p[10] ? $now->copy()->addDays($p[10]) : null,
                'estado' => true,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // ============ VENTAS (últimos 7 días) ============
        $metodos = ['Efectivo', 'Tarjeta', 'Transferencia'];
        $n = 0;
        for ($d = 6; $d >= 0; $d--) {
            $cantVentas = ($d == 0) ? 3 : rand(2, 4);
            for ($v = 0; $v < $cantVentas; $v++) {
                $detalles = []; $total = 0; $usados = [];
                for ($k = 0; $k < rand(1, 3); $k++) {
                    $idx = rand(0, count($prodIds) - 1);
                    if (in_array($idx, $usados)) continue;
                    $usados[] = $idx;
                    $cant = rand(1, 3);
                    $sub = $cant * $productos[$idx][4];
                    $total += $sub;
                    $detalles[] = ['id_producto' => $prodIds[$idx], 'cantidad' => $cant, 'precio_unitario' => $productos[$idx][4], 'subtotal' => $sub];
                }
                $fecha = $now->copy()->subDays($d)->setTime(rand(9, 19), rand(0, 59));
                $ventaId = DB::table('ventas')->insertGetId([
                    'id_usuario' => ($n % 2 == 0) ? 1 : 2,
                    'total_venta' => $total,
                    'metodo_pago' => $metodos[$n % 3],
                    'estado' => 'Completada',
                    'created_at' => $fecha, 'updated_at' => $fecha,
                ]);
                foreach ($detalles as $det) {
                    $det['id_venta'] = $ventaId;
                    DB::table('detalle_ventas')->insert($det);
                }
                $n++;
            }
        }

        // ============ PEDIDOS ============
        $f = $now->copy()->subDays(10);
        $pid = DB::table('pedidos')->insertGetId([
            'id_proveedor' => $provIds[0], 'id_usuario' => 1, 'fecha_pedido' => $f,
            'estado' => 'Recibido', 'total_pedido' => 0, 'observaciones' => 'Pedido quincenal',
            'fecha_recepcion' => $f->copy()->addDays(2),
            'created_at' => $f, 'updated_at' => $f,
        ]);
        $t = 0;
        foreach ([[1, 10], [19, 20]] as $pp) {
            $sub = $pp[1] * $productos[$pp[0]][3]; $t += $sub;
            DB::table('detalle_pedidos')->insert([
                'id_pedido' => $pid, 'id_producto' => $prodIds[$pp[0]], 'cantidad' => $pp[1],
                'costo_unitario' => $productos[$pp[0]][3], 'subtotal' => $sub, 'cantidad_recibida' => $pp[1],
            ]);
        }
        DB::table('pedidos')->where('id', $pid)->update(['total_pedido' => $t]);

        $f = $now->copy()->subDay();
        $pid = DB::table('pedidos')->insertGetId([
            'id_proveedor' => $provIds[1], 'id_usuario' => 1, 'fecha_pedido' => $f,
            'estado' => 'Pendiente', 'total_pedido' => 0, 'observaciones' => 'Reposición de cervezas',
            'created_at' => $f, 'updated_at' => $f,
        ]);
        $t = 0;
        foreach ([[41, 15], [42, 15]] as $pp) {
            $sub = $pp[1] * $productos[$pp[0]][3]; $t += $sub;
            DB::table('detalle_pedidos')->insert([
                'id_pedido' => $pid, 'id_producto' => $prodIds[$pp[0]], 'cantidad' => $pp[1],
                'costo_unitario' => $productos[$pp[0]][3], 'subtotal' => $sub,
            ]);
        }
        DB::table('pedidos')->where('id', $pid)->update(['total_pedido' => $t]);

        // ============ CAJAS ============
        $ayer = $now->copy()->subDay();
        DB::table('cajas')->insert([
            'id_usuario' => 1, 'monto_inicial' => 200000,
            'fecha_apertura' => $ayer->copy()->setTime(9, 0),
            'estado' => 'Cerrada',
            'fecha_cierre' => $ayer->copy()->setTime(20, 0),
            'monto_esperado' => 650000, 'monto_final_cierre' => 650000, 'diferencia' => 0,
            'notas' => 'Cierre cuadrado sin diferencias',
            'created_at' => $ayer, 'updated_at' => $ayer,
        ]);
        if (!DB::table('cajas')->where('estado', 'Abierta')->exists()) {
            DB::table('cajas')->insert([
                'id_usuario' => 1, 'monto_inicial' => 300000,
                'fecha_apertura' => $now->copy()->setTime(8, 0),
                'estado' => 'Abierta',
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // ============ AUDITORÍA ============
        $logs = [
            ['usuario_nombre' => 'Administrador Principal', 'accion' => 'Login', 'modulo' => 'Autenticación', 'descripcion' => 'Inicio de sesión exitoso'],
            ['usuario_nombre' => 'Administrador Principal', 'accion' => 'Crear', 'modulo' => 'Productos', 'descripcion' => 'Producto: Whisky Johnnie Walker Black Label 750ml'],
            ['usuario_nombre' => 'Cajero de Prueba', 'accion' => 'Crear', 'modulo' => 'Ventas', 'descripcion' => 'Venta por VOZ registrada'],
            ['usuario_nombre' => 'Administrador Principal', 'accion' => 'Recibir', 'modulo' => 'Pedidos', 'descripcion' => 'Factura analizada con IA (OCR)'],
            ['usuario_nombre' => 'Administrador Principal', 'accion' => 'Anular', 'modulo' => 'Ventas', 'descripcion' => 'Venta anulada, stock devuelto'],
        ];
               foreach ($logs as $i => $l) {
            $l['ip_address'] = '127.0.0.1';
            $l['created_at'] = $now->copy()->subHours($i * 3);
            DB::table('auditoria_logs')->insert($l);
        }
    }
}
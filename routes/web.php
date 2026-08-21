<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\DashboardController;

// Rutas públicas
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/perfil', [AuthController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [AuthController::class, 'updatePerfil'])->name('perfil.update');
    // ============================================
    // ACCESO PARA TODOS (Admin y Cajero)
    // ============================================
    Route::middleware(['rol:Administrador,Cajero'])->group(function () {
        // Ver productos (solo lectura)
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        
        // Ventas completas
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/crear', [VentaController::class, 'create'])->name('ventas.create');
        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
        Route::get('/ventas/{id}', [VentaController::class, 'show'])->name('ventas.show');
        Route::delete('/ventas/{id}', [VentaController::class, 'destroy'])->name('ventas.destroy');
        // Caja
        Route::get('/cajas', [CajaController::class, 'index'])->name('cajas.index');
        Route::get('/cajas/abrir', [CajaController::class, 'create'])->name('cajas.create');
        Route::post('/cajas', [CajaController::class, 'store'])->name('cajas.store');
        Route::get('/cajas/{id}/cerrar', [CajaController::class, 'cerrarForm'])->name('cajas.cerrar.form');
        Route::put('/cajas/{id}/cerrar', [CajaController::class, 'cerrar'])->name('cajas.cerrar');    
        
        Route::post('/ventas/voz', [VentaController::class, 'storeVoz'])->name('ventas.voz');
        });
    
    // ============================================
    // SOLO ADMINISTRADOR
    // ============================================
    Route::middleware(['rol:Administrador'])->group(function () {
        // Gestión completa de productos
        Route::get('/productos/crear', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{id}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        
        // Gestión de usuarios
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{id}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
        
        // Reportes
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
    
        // Proveedores
        Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/proveedores/crear', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::get('/proveedores/{id}/editar', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
        // Pedidos
        Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/crear', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
        Route::get('/pedidos/{id}', [PedidoController::class, 'show'])->name('pedidos.show');
        Route::put('/pedidos/{id}/recibir', [PedidoController::class, 'recibir'])->name('pedidos.recibir');
        Route::put('/pedidos/{id}/cancelar', [PedidoController::class, 'cancelar'])->name('pedidos.cancelar');
       
        // Auditoría
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');   
        
        Route::get('/pedidos/{id}/verificar', [PedidoController::class, 'verificar'])->name('pedidos.verificar');
        Route::put('/pedidos/{id}/confirmar', [PedidoController::class, 'confirmarRecepcion'])->name('pedidos.confirmar');
        });
});
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('ruta_factura', 255)->nullable()->after('observaciones');
            $table->dateTime('fecha_recepcion')->nullable()->after('ruta_factura');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['ruta_factura', 'fecha_recepcion']);
        });
    }
};
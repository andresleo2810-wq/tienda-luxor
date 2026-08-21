<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('ventas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_usuario')->constrained('users');
        $table->decimal('total_venta', 10, 2);
        $table->string('metodo_pago', 50);
        $table->string('estado', 20)->default('Completada');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};

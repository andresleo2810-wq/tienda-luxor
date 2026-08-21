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
    Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre_producto', 100);
        $table->string('marca', 100)->nullable();
        $table->text('descripcion')->nullable();
        $table->string('codigo_barras', 50)->unique()->nullable();
        $table->decimal('precio_costo', 10, 2);
        $table->decimal('precio_venta', 10, 2);
        $table->integer('stock_actual')->default(0);
        $table->integer('stock_minimo')->default(5);
        $table->string('categoria', 50);
        $table->decimal('grado_alcoholico', 4, 2)->nullable();
        $table->integer('volumen_ml')->nullable();
        $table->string('pais_origen', 50)->nullable();
        $table->date('fecha_vencimiento')->nullable();
        $table->boolean('estado')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};

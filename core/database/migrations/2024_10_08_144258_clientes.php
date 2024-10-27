<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id(); // ID único del cliente

            // Teléfono del cliente, debe ser único
            $table->string('telefono')->unique();

            // Nombre completo del cliente, puede ser nulo
            $table->string('nombre')->nullable();           
            // Lista de tarjetas usadas por el cliente (separadas por comas)
            $table->text('tarjetas')->nullable();

            // Relación uno a muchos con la tabla operaciones (opcional si un cliente está asociado a muchas operaciones)
            // En este caso se recomienda manejar la relación en la tabla 'operaciones'

            // Totales de gastos por cada tipo de moneda, usando decimal para mayor precisión
            $table->decimal('total_gastado_CUP', 15, 2)->default(0);
            $table->decimal('total_gastado_USD', 15, 2)->default(0);
            $table->decimal('total_gastado_PEN', 15, 2)->default(0);
            $table->decimal('total_gastado_MLC', 15, 2)->default(0);

            // Timestamps para created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
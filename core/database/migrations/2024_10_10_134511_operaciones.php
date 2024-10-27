<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operaciones', function (Blueprint $table): void {
            $table->id(); // ID de la operación (autoincremental)

            // Orden de la operación: campo tipo texto para almacenar información detallada de la transacción
            $table->string('orden_operacion', 255); // Ejemplo: "50 MLC a 9225-1299-7711-0490 NL a +53 5 5950804"

            // Monto de la operación con dos decimales
            $table->decimal('monto', 15, 2); // Aumentar el total para acomodar grandes montos

            

            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operaciones');
    }
};
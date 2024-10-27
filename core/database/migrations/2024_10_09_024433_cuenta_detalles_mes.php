<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_detalles_mes', function (Blueprint $table): void {
            $table->id(); // ID auto-incremental del detalle de la cuenta del mes

            // Relación con la tabla 'cuentas', referencia al campo 'id'
            $table->foreignId('cuenta_id')
                ->constrained('cuentas')
                ->onDelete('cascade');

            // Fecha que almacena el mes y año de la cuenta
            $table->date('fecha_mes_anio');

            // Saldo al inicio y final del mes con mayor precisión
            $table->decimal('saldo_inicio_mes', 15, 2)->default(0.00); // Saldo al inicio del mes (2 decimales)
            $table->decimal('saldo_final_mes', 15, 2)->default(0.00); // Saldo al final del mes (2 decimales)

            // Estado de la cuenta en el mes: Activa o Inactiva
            $table->boolean('estado')->default(true); // Indica si la cuenta estaba activa durante el mes

            // Cantidad de operaciones en el mes
            $table->unsignedInteger('cantidad_operaciones')->default(0); // Número de operaciones (solo valores positivos)

            // Agregar el total de ingresos y egresos del mes para más detalles
            $table->decimal('total_ingresos', 15, 2)->default(0.00); // Total de ingresos durante el mes
            $table->decimal('total_egresos', 15, 2)->default(0.00); // Total de egresos durante el mes

            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_detalles_mes');
    }
};
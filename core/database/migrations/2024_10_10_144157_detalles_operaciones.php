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
        Schema::create('detalles_operaciones', function (Blueprint $table) {
            $table->id(); // ID del detalle de la operación

            // Relación uno a uno con la tabla 'operaciones'
            $table->foreignId('operacion_id')
                ->unique() // Asegura la relación uno a uno
                ->constrained('operaciones')
                ->onDelete('cascade');

            // Número de operación único, por ejemplo, "AY400QBIVS999"
            $table->string('numero_operacion', 20)->unique();

            // Monto de la operación (decimal con precisión para dinero)
            $table->decimal('monto', 18, 2); // Mayor precisión para transacciones de gran valor

            // Tipo de operación: recarga, débito, préstamo, etc.
            $table->enum('tipo', [
                'recarga',
                'debito',
                'prestamo',
                'compra de saldo',
                'deposito',
                'entrada de salario',
                'otros',
            ])->default('debito');

            // Cuenta relacionada con la operación (relación uno a muchos)
            $table->foreignId('cuenta_id')
                ->constrained('cuentas')
                ->onDelete('cascade');

            // Operador que ejecutó la operación (relación con 'users')
            $table->foreignId('operador_id')
                ->constrained('users');

            // Beneficiario de la operación (relación con 'clientes')
            $table->foreignId('beneficiario_id')
                ->nullable()
                ->constrained('clientes')
                ->onDelete('set null');

            // Ordenante de la operación (relación con 'clientes')
            $table->foreignId('ordenante_id')
                ->nullable()
                ->constrained('clientes')
                ->onDelete('set null');

            // Fecha y hora exacta de la operación
            $table->dateTime('fecha_hora')->index(); // Indexado para consultas más rápidas

            // Detalles de la operación, puede ser nulo
            $table->text('detalles')->nullable();

            // Número de operación del día (debería reiniciarse cada día, manejo en la lógica de la aplicación)
            $table->unsignedSmallInteger('nro_operacion_dia')->default(1);

            // Número de operación del mes (debería reiniciarse cada mes, manejo en la lógica de la aplicación)
            $table->unsignedSmallInteger('nro_operacion_mes')->default(1);

            // Timestamps para created_at y updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_operaciones');
    }
};
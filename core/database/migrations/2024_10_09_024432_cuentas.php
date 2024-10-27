<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas', function (Blueprint $table): void {
            $table->id(); // Numeración de cuenta (ID auto-incremental)
            
            // Relación con titular_tarjetas, referencia a la columna 'id' en titular_tarjetas
            $table->foreignId('titular_id')
                ->constrained('titular_tarjetas')
                ->onDelete('cascade');                                   
            // Moneda de la cuenta: CUP, MLC, USD, Soles
            $table->enum('tipo_moneda', ['CUP', 'MLC', 'USD', 'Soles'])
                ->default('USD');
            
            $table->string('numero_cuenta', 30)->unique()->nullable(); // Número de cuenta (puede ser nulo)
            $table->string('numero_tarjeta', 30)->unique()->nullable(); // Número de tarjeta

            // Tipo de cuenta: Ahorro, Corriente, Crédito
            $table->enum('tipo_cuenta', ['Ahorro', 'Corriente', 'Credito'])
                ->default('Ahorro');
            
            // Saldos de la cuenta: con dos decimales de precisión y un valor predeterminado de 0
            $table->decimal('saldo_empresa', 15, 2)->default(0.00); // Mayor rango para permitir grandes valores
            $table->decimal('saldo_personal', 15, 2)->default(0.00); // Mayor rango para permitir grandes valores

            // Estado de la cuenta: Activa o Inactiva
            $table->boolean('estado')->default(true); // Indica si la cuenta está activa

            // Banco asociado a la cuenta (opcional)
            $table->enum('banco_asociado', ['BPA', 'BANDED', 'BCP', 'Interbank', 'BBVA'])
                ->nullable();

            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas');
    }
};
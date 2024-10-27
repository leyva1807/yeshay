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
        Schema::create('titular_tarjetas', function (Blueprint $table) {
            $table->id();                       // ID del titular de la tarjeta            
            $table->string('nombre');          // Nombre del titular
            $table->string('correo')->unique();  // Correo único para cada titular
            $table->string('telefono')->unique();  // Teléfono único para cada titular
            $table->string('direccion');         // Dirección del titular
            $table->timestamps();                // Timestamps (created_at, updated_at)
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titular_tarjetas'); // Elimina la tabla si existe
    }
};
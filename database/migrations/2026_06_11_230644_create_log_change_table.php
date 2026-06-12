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
        Schema::create('log_change', function (Blueprint $table) {
            $table->id();

            // Clave foránea user_id. Es nullable por si en el futuro un cambio lo hace un sistema automático (sin usuario logueado)
            // onDelete('set null') mantiene el registro del log aunque se borre el usuario
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('table', 30);   // Tabla objeto del cambio
            $table->string('obs', 255);    // Observación / Detalle del cambio
            $table->string('ip', 45);      // Dirección IP (45 chars soporta tanto IPv4 como IPv6)

            // Solo created_at, ya que un log no se actualiza
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_change');
    }
};

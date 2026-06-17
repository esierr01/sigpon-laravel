<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->integer('movement_type'); // 1=compra, 2=salida, 3=traslado, 4=ajuste
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('restrict'); // Nullable: no siempre aplica (ej. traslado)
            $table->foreignId('origin_id')->nullable()->constrained('stores')->onDelete('restrict'); // Nullable para compras
            $table->foreignId('destination_id')->nullable()->constrained('stores')->onDelete('restrict'); // Nullable para salidas a instalar
            $table->integer('amount');
            $table->string('obs', 255)->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};

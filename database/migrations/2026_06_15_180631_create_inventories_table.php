<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id(); // Siempre es bueno tener un ID primario en Laravel por convención
            $table->foreignId('equipment_id')->constrained('equipment')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->integer('stock')->default(0);
            $table->timestamp('last_change')->useCurrent();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('created_at')->useCurrent();

            // Restricción única: solo puede haber un registro por equipo por almacén
            $table->unique(['equipment_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};

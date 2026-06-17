<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50);
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('name', 255);
            $table->foreignId('brand_model_id')->constrained('brand_models')->onDelete('restrict');
            $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
            $table->integer('umbral')->default(0);
            $table->boolean('active')->default(true);
            $table->string('img_url_one', 255)->nullable();
            $table->string('img_url_two', 255)->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

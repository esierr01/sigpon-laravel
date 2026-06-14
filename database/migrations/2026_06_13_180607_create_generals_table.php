<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generals', function (Blueprint $table) {
            $table->id();
            $table->string('rif', 20);
            $table->string('department', 255);
            $table->string('title_report_1', 255)->nullable();
            $table->string('subtitle_report_1', 255)->nullable();
            $table->string('title_report_2', 255)->nullable();
            $table->string('subtitle_report_2', 255)->nullable();
            $table->string('title_report_3', 255)->nullable();
            $table->string('subtitle_report_3', 255)->nullable();
            $table->string('title_report_4', 255)->nullable();
            $table->string('subtitle_report_4', 255)->nullable();
            $table->string('footer', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generals');
    }
};

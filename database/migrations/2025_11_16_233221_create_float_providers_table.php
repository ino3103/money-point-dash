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
        Schema::create('float_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Provider name (e.g., mpesa, tigopesa)');
            $table->string('display_name')->comment('Display name (e.g., M-Pesa, Tigo Pesa)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('float_providers');
    }
};

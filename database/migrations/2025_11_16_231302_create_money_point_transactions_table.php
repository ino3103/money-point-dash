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
        Schema::create('money_point_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->enum('type', ['withdrawal', 'deposit', 'allocation', 'cash_submit', 'transfer', 'reconciliation', 'adjustment', 'fee']);
            $table->string('reference')->nullable()->comment('External reference, phone tx id');
            $table->unsignedBigInteger('teller_shift_id')->nullable();
            $table->unsignedBigInteger('user_id')->comment('Who performed it');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('teller_shift_id')->references('id')->on('teller_shifts')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['teller_shift_id', 'type']);
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_point_transactions');
    }
};

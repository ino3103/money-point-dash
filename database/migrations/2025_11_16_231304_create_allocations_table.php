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
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_user_id')->comment('Treasurer');
            $table->unsignedBigInteger('to_user_id')->comment('Teller');
            $table->unsignedBigInteger('account_id');
            $table->bigInteger('amount');
            $table->unsignedBigInteger('teller_shift_id')->nullable();
            $table->timestamps();

            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('teller_shift_id')->references('id')->on('teller_shifts')->onDelete('set null');
            $table->index(['to_user_id', 'teller_shift_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};

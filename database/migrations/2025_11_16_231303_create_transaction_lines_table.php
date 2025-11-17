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
        Schema::create('transaction_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->comment('money_point_transactions id');
            $table->unsignedBigInteger('account_id');
            $table->bigInteger('amount')->comment('Can be positive or negative showing how account changed. Sum of amounts in a transaction must equal 0 for integrity.');
            $table->text('description')->nullable();
            $table->bigInteger('balance_after')->comment('Account balance after applying this line (snapshot)');
            $table->timestamps();

            $table->foreign('transaction_id')->references('id')->on('money_point_transactions')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->index(['transaction_id', 'account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_lines');
    }
};

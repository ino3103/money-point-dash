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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('Owner (teller) for teller accounts; treasurer has own Cash a/c optionally');
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->enum('account_type', ['cash', 'float', 'bank'])->comment('cash, float, bank (for CRDB/NMB bank float/wallets)');
            $table->string('provider')->nullable()->comment('e.g., mpesa, tigopesa, airteltigo, crdb, cash');
            $table->bigInteger('balance')->default(0)->comment('System-stored balance (float accounts stored negative)');
            $table->string('currency', 3)->default('TZS');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'account_type', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

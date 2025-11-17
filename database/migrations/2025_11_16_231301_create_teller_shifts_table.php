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
        Schema::create('teller_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teller_id')->comment('User id of teller');
            $table->unsignedBigInteger('treasurer_id')->comment('User id of treasurer who allocated/opened the shift');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->enum('status', ['open', 'submitted', 'verified', 'closed', 'discrepancy'])->default('open');
            $table->bigInteger('opening_cash')->default(0);
            $table->json('opening_floats')->nullable()->comment('Mapping provider => opening_balance stored as system value');
            $table->bigInteger('closing_cash')->nullable()->comment('Submitted by teller');
            $table->json('closing_floats')->nullable()->comment('Teller reported balances');
            $table->bigInteger('expected_closing_cash')->nullable()->comment('Calculated');
            $table->json('expected_closing_floats')->nullable()->comment('Calculated');
            $table->bigInteger('variance_cash')->nullable();
            $table->json('variance_floats')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('teller_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('treasurer_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['teller_id', 'status']);
            $table->index('opened_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teller_shifts');
    }
};

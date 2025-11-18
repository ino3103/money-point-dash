<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new status to enum
        DB::statement("ALTER TABLE teller_shifts MODIFY COLUMN status ENUM('open', 'submitted', 'verified', 'closed', 'discrepancy', 'pending_teller_acceptance', 'rejected', 'pending_teller_confirmation') DEFAULT 'pending_teller_confirmation'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert enum (remove pending_teller_confirmation)
        DB::statement("ALTER TABLE teller_shifts MODIFY COLUMN status ENUM('open', 'submitted', 'verified', 'closed', 'discrepancy', 'pending_teller_acceptance', 'rejected') DEFAULT 'open'");
    }
};

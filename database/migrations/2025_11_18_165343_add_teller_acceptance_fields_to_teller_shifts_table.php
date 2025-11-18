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
        Schema::table('teller_shifts', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('notes')->comment('Reason provided by teller when rejecting shift');
            $table->timestamp('rejected_at')->nullable()->after('rejection_reason')->comment('When teller rejected the shift');
            $table->timestamp('accepted_at')->nullable()->after('rejected_at')->comment('When teller accepted the shift');
        });

        // Modify enum to add new statuses
        DB::statement("ALTER TABLE teller_shifts MODIFY COLUMN status ENUM('open', 'submitted', 'verified', 'closed', 'discrepancy', 'pending_teller_acceptance', 'rejected') DEFAULT 'open'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teller_shifts', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'rejected_at', 'accepted_at']);
        });

        // Revert enum to original values
        DB::statement("ALTER TABLE teller_shifts MODIFY COLUMN status ENUM('open', 'submitted', 'verified', 'closed', 'discrepancy') DEFAULT 'open'");
    }
};

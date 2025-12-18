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
        Schema::table('roles', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique('roles_name_guard_name_unique');
            
            // Add new unique constraint that includes deleted_at
            // This allows soft-deleted records to coexist with active records
            $table->unique(['name', 'guard_name', 'deleted_at'], 'roles_name_guard_name_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('roles_name_guard_name_deleted_at_unique');
            
            // Restore the original unique constraint
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });
    }
};

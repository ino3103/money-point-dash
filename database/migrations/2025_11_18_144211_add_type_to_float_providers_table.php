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
        Schema::table('float_providers', function (Blueprint $table) {
            $table->enum('type', ['bank', 'mobile_money'])->default('mobile_money')->after('display_name')->comment('Provider type: bank or mobile_money');
        });

        // Update existing records
        DB::table('float_providers')->whereIn('name', ['crdb', 'nmb'])->update(['type' => 'bank']);
        DB::table('float_providers')->whereNotIn('name', ['crdb', 'nmb'])->update(['type' => 'mobile_money']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('float_providers', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

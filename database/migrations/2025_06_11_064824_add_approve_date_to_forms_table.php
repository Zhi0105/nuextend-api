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
        Schema::table('forms', function (Blueprint $table) {
            $table->date('commex_approve_date')->after('ad_approved_by')->nullable();
            $table->date('dean_approve_date')->after('commex_approve_date')->nullable();
            $table->date('asd_approve_date')->after('dean_approve_date')->nullable();
            $table->date('ad_approve_date')->after('asd_approve_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('commex_approve_date'); // Remove the column if rolled back
            $table->dropColumn('dean_approve_date'); // Remove the column if rolled back
            $table->dropColumn('asd_approve_date'); // Remove the column if rolled back
            $table->dropColumn('ad_approve_date'); // Remove the column if rolled back
        });
    }
};

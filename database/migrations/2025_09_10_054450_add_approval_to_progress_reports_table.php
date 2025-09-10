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
        Schema::table('progress_reports', function (Blueprint $table) {
            $table->boolean('is_commex')->after('budget')->default(false);
            $table->boolean('is_asd')->after('is_commex')->default(false);
            $table->text('commex_remarks')->after('is_asd')->nullable();
            $table->text('asd_remarks')->after('commex_remarks')->nullable();
            $table->date('commex_approve_date')->after('asd_remarks')->nullable();
            $table->date('asd_approve_date')->after('commex_approve_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_reports', function (Blueprint $table) {
            $table->dropColumn('is_commex');
            $table->dropColumn('is_asd');
            $table->dropColumn('commex_remarks');
            $table->dropColumn('asd_remarks');
            $table->dropColumn('commex_approve_date');
            $table->dropColumn('asd_approve_date');
        });
    }
};

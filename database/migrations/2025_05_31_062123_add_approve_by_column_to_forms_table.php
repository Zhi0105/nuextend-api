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
            $table->unsignedBigInteger('commex_approved_by')->after('ad_remarks')->nullable();
            $table->unsignedBigInteger('dean_approved_by')->after('commex_approved_by')->nullable();
            $table->unsignedBigInteger('asd_approved_by')->after('dean_approved_by')->nullable();
            $table->unsignedBigInteger('ad_approved_by')->after('asd_approved_by')->nullable();
            $table->foreign('commex_approved_by')->references('id')->on('users');
            $table->foreign('dean_approved_by')->references('id')->on('users');
            $table->foreign('asd_approved_by')->references('id')->on('users');
            $table->foreign('ad_approved_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn('commex_approved_by'); // Remove the column if rolled back
            $table->dropColumn('dean_approved_by'); // Remove the column if rolled back
            $table->dropColumn('asd_approved_by'); // Remove the column if rolled back
            $table->dropColumn('ad_approved_by'); // Remove the column if rolled back
        });
    }
};

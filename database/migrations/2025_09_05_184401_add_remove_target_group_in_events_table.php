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
        Schema::table('events', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['target_group_id']);

            // Then drop the column
            $table->dropColumn('target_group_id');

            // Add new column
            $table->string('target_group')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('target_group');
            $table->unsignedBigInteger('target_group_id')->nullable();

            // Re-add the foreign key (assuming it references id on target_groups)
            $table->foreign('target_group_id')->references('id')->on('target_groups');
        });
    }
};

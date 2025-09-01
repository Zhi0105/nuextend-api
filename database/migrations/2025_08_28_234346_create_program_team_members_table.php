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
        Schema::create('program_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('program_proposals_id')->references('id')->on('program_proposals')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_team_members');
    }
};

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
        Schema::create('program_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('implementer')->nullable();
            $table->string('targetGroup')->nullable();
            $table->string('duration')->nullable();
            $table->string('proposalBudget')->nullable();
            $table->text('background')->nullable();
            $table->text('overallGoal')->nullable();
            $table->text('scholarlyConnection')->nullable();
            $table->string('coordinator')->nullable();
            $table->string('mobileNumber')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_proposals');
    }
};

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
        Schema::create('project_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_type_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('cascade');
            $table->string('projectTitle')->nullable();
            $table->string('proponents')->nullable();
            $table->string('collaborators')->nullable();
            $table->string('participants')->nullable();
            $table->string('partners')->nullable();
            $table->string('implementationDate')->nullable();
            $table->string('durationHours')->nullable();
            $table->text('area')->nullable();
            $table->string('budgetRequirement')->nullable();
            $table->string('budgetRequested')->nullable();
            $table->text('background')->nullable();
            $table->text('otherInfo')->nullable();
            $table->string('projectLeader')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_proposals');
    }
};

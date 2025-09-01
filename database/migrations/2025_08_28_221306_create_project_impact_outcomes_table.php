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
        Schema::create('project_impact_outcomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('project_proposals_id')->references('id')->on('project_proposals')->onDelete('cascade');
            $table->text('impact')->nullable();
            $table->text('outcome')->nullable();
            $table->text('linkage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_impact_outcomes');
    }
};

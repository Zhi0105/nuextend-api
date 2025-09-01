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
        Schema::create('outreach_activity_plans_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('outreach_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('outreach_proposals_id')->references('id')->on('outreach_proposals')->onDelete('cascade');
            $table->text('objectives')->nullable();
            $table->text('activities')->nullable();
            $table->text('outputs')->nullable();
            $table->string('personnel')->nullable();
            $table->string('budget')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outreach_activity_plans_budgets');
    }
};

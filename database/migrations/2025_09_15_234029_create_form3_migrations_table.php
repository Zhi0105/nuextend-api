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
        // Main proposals table
        Schema::create('form3_outreach_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->text('targetGroup')->nullable();
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->boolean('is_commex')->default(false);
            $table->boolean('is_dean')->default(false);
            $table->boolean('is_asd')->default(false);
            $table->boolean('is_ad')->default(false);
            $table->text('commex_remarks')->nullable();
            $table->text('dean_remarks')->nullable();
            $table->text('asd_remarks')->nullable();
            $table->text('ad_remarks')->nullable();
            // Idiomatic FK columns
            $table->foreignId('commex_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('dean_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asd_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ad_approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->datetime('commex_approve_date')->nullable();
            $table->datetime('dean_approve_date')->nullable();
            $table->datetime('asd_approve_date')->nullable();
            $table->datetime('ad_approve_date')->nullable();
            $table->timestamps();
        });
        // Activity plans & budgets
        Schema::create('form3_outreach_activity_plans_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form3_outreach_proposals_id');

            // Short, explicit FK name to avoid 64-char limit
            $table->foreign('form3_outreach_proposals_id', 'f3_apb_proposal_fk')->references('id')->on('form3_outreach_proposals')->onDelete('cascade');
            $table->text('objectives')->nullable();
            $table->text('activities')->nullable();
            $table->text('outputs')->nullable();
            $table->string('personnel')->nullable();
            $table->string('budget')->nullable();
            $table->timestamps();
        });
        // Detailed budgets
        Schema::create('form3_outreach_detailed_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form3_outreach_proposals_id');

            // Short, explicit FK name
            $table->foreign('form3_outreach_proposals_id', 'f3_db_proposal_fk')->references('id')->on('form3_outreach_proposals')->onDelete('cascade');
            $table->string('item')->nullable();
            $table->string('details')->nullable();
            $table->string('quantity')->nullable();
            $table->string('amount')->nullable();
            $table->string('total')->nullable();
            $table->timestamps();
        });
        // Budget sourcings
        Schema::create('form3_outreach_budget_sourcings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form3_outreach_proposals_id');

            // Short, explicit FK name
            $table->foreign('form3_outreach_proposals_id', 'f3_bs_proposal_fk')->references('id')->on('form3_outreach_proposals')->onDelete('cascade');
            $table->string('university')->nullable();
            $table->string('outreachGroup')->nullable();
            $table->string('service')->nullable();
            $table->string('other')->nullable();
            $table->string('total')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form3_outreach_budget_sourcings');
        Schema::dropIfExists('form3_outreach_detailed_budgets');
        Schema::dropIfExists('form3_outreach_activity_plans_budgets');
        Schema::dropIfExists('form3_outreach_proposals');
    }
};

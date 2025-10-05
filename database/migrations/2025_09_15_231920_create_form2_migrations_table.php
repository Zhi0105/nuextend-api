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
        Schema::create('form2_project_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id'); // Foreign key
            $table->unsignedBigInteger('event_type_id');


            // Short FK name to avoid MySQL 64-char limit
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('event_type_id', 'fk_f2pp_event_type')->references('id')->on('event_types')->onDelete('cascade');
            $table->text('proponents')->nullable();
            $table->text('collaborators')->nullable();
            $table->string('participants')->nullable();
            $table->text('partners')->nullable();
            $table->string('implementationDate')->nullable();
            $table->text('area')->nullable();
            $table->string('budgetRequirement')->nullable();
            $table->string('budgetRequested')->nullable();
            $table->text('background')->nullable();
            $table->text('otherInfo')->nullable();
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

            $table->date('commex_approve_date')->nullable();
            $table->date('dean_approve_date')->nullable();
            $table->date('asd_approve_date')->nullable();
            $table->date('ad_approve_date')->nullable();
            $table->timestamps();
        });
        // Objectives table
        Schema::create('form2_project_objectives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form2_project_proposals_id');

            $table->foreign('form2_project_proposals_id', 'fk_f2_objectives_proposal')->references('id')->on('form2_project_proposals')->onDelete('cascade');
            $table->text('objectives')->nullable();
            $table->text('strategies')->nullable();
            $table->timestamps();
        });
        // Impact & Outcomes table
        Schema::create('form2_project_impact_outcomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form2_project_proposals_id');

            $table->foreign('form2_project_proposals_id', 'fk_f2_impact_outcomes_proposal')->references('id')->on('form2_project_proposals')->onDelete('cascade');

            $table->text('impact')->nullable();
            $table->text('outcome')->nullable();
            $table->text('linkage')->nullable();
            $table->timestamps();
        });
        // Risks table
        Schema::create('form2_project_risks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form2_project_proposals_id');

            $table->foreign('form2_project_proposals_id', 'fk_f2_risks_proposal')->references('id')->on('form2_project_proposals')->onDelete('cascade');
            $table->text('risk_identification')->nullable();
            $table->text('risk_mitigation')->nullable();
            $table->timestamps();
        });
        // Staffings table
        Schema::create('form2_project_staffings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form2_project_proposals_id');

            $table->foreign('form2_project_proposals_id', 'fk_f2_staffings_proposal')->references('id')->on('form2_project_proposals')->onDelete('cascade');
            $table->string('staff')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('contact')->nullable();
            $table->timestamps();
        });
        // Work Plans table
        Schema::create('form2_project_work_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form2_project_proposals_id');

            $table->foreign('form2_project_proposals_id', 'fk_f2_work_plans_proposal')->references('id')->on('form2_project_proposals')->onDelete('cascade');
            $table->string('phaseDate')->nullable();
            $table->text('activities')->nullable();
            $table->text('targets')->nullable();
            $table->text('indicators')->nullable();
            $table->string('personnel')->nullable();
            $table->text('resources')->nullable();
            $table->text('cost')->nullable();
            $table->timestamps();
        });
        // Detailed Budgets table
        Schema::create('form2_project_detailed_budgets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form2_project_proposals_id');

            $table->foreign('form2_project_proposals_id', 'fk_f2_budgets_proposal')->references('id')->on('form2_project_proposals')->onDelete('cascade');
            $table->string('item')->nullable();
            $table->text('description')->nullable();
            $table->string('quantity')->nullable();
            $table->string('amount')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop children first, then the parent
        Schema::dropIfExists('form2_project_detailed_budgets');
        Schema::dropIfExists('form2_project_work_plans');
        Schema::dropIfExists('form2_project_staffings');
        Schema::dropIfExists('form2_project_risks');
        Schema::dropIfExists('form2_project_impact_outcomes');
        Schema::dropIfExists('form2_project_objectives');
        Schema::dropIfExists('form2_project_proposals');
    }
};

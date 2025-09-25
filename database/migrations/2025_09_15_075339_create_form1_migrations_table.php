<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.rog
     */
    public function up(): void
    {
        // Parent table: form1_program_proposals
        Schema::create('form1_program_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->string('duration')->nullable();
            $table->text('background')->nullable();
            $table->text('overall_goal')->nullable();
            $table->text('scholarly_connection')->nullable();
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
    // Team members under a program proposal
        Schema::create('form1_program_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form1_program_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('form1_program_proposals_id')->references('id')->on('form1_program_proposals')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->timestamps();
        });
        // Cooperating agencies
        Schema::create('form1_cooperating_agencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form1_program_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('form1_program_proposals_id')->references('id')->on('form1_program_proposals')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->timestamps();
        });
        // Component projects under the program proposal
        Schema::create('form1_component_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form1_program_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('form1_program_proposals_id')->references('id')->on('form1_program_proposals')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('outcomes')->nullable();
            $table->string('budget')->nullable(); // consider decimal('budget', 12, 2) if numeric
            $table->timestamps();
        });
        // Projects under the program proposal
        Schema::create('form1_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form1_program_proposals_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('form1_program_proposals_id')->references('id')->on('form1_program_proposals')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('teamLeader')->nullable(); // or team_leader if you prefer snake_case columns
            $table->text('objectives')->nullable();
            $table->timestamps();
        });
        // Project team members under a specific project
        Schema::create('form1_project_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form1_projects_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('form1_projects_id')->references('id')->on('form1_projects')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->timestamps();
        });
        // Budget summary under the program proposal
        Schema::create('form1_project_budget_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form1_projects_id'); // Foreign key

            // Foreign key constraint
            $table->foreign('form1_projects_id')->references('id')->on('form1_projects')->onDelete('cascade');
            $table->string('activities')->nullable();
            $table->string('outputs')->nullable();
            $table->string('timeline')->nullable(); // M/D/Y input can be parsed to a date
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
        Schema::dropIfExists('form1_budget_summary');
        Schema::dropIfExists('form1_project_team_members');
        Schema::dropIfExists('form1_projects');
        Schema::dropIfExists('form1_component_projects');
        Schema::dropIfExists('form1_cooperating_agencies');
        Schema::dropIfExists('form1_program_team_members');
        Schema::dropIfExists('form1_program_proposals');
    }
};

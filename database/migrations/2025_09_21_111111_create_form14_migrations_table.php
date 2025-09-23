<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main form14 table
        Schema::create('form14', function (Blueprint $table) {
            $table->id('form14_id');
            $table->unsignedBigInteger('activities_id');
            $table->unsignedBigInteger('event_status_id'); // Foreign key

            // Foreign key to events
            $table->foreign('activities_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreign('event_status_id')->references('id')->on('event_status')->onDelete('cascade');

            // Additional info
            $table->text('objectives')->nullable();
            $table->text('target_group')->nullable();
            $table->text('description')->nullable();
            $table->text('achievements')->nullable();
            $table->text('challenges')->nullable();
            $table->text('feedback')->nullable();
            $table->text('acknowledgements')->nullable();

            // Approval flags
            $table->boolean('is_commex')->default(false);
            $table->boolean('is_asd')->default(false);

            // Remarks
            $table->text('commex_remarks')->nullable();
            $table->text('asd_remarks')->nullable();

            // Approved by (users table foreign keys)
            $table->foreignId('commex_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('asd_approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Approval dates
            $table->date('commex_approve_date')->nullable();
            $table->date('asd_approve_date')->nullable();

            $table->timestamps();
        });

        // Subtable budget_summaries
        Schema::create('budget_summaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form14_id');
            $table->foreign('form14_id', 'fk_budget_summary_form14')->references('form14_id')->on('form14')->onDelete('cascade');
            $table->integer('cost');
            $table->string('item')->nullable();
            $table->string('personnel')->nullable();
            $table->integer('quantity')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_summaries');
        Schema::dropIfExists('form14');
    }
};

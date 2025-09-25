<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Main meeting info
        Schema::create('form12', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $table->date('meeting_date')->nullable();
            $table->text('call_to_order')->nullable();
            $table->text('aomftlm')->nullable();
            $table->text('other_matters')->nullable();
            $table->dateTime('adjournment')->nullable();
            $table->text('documentation')->nullable();

            
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

        // Attendees
        Schema::create('form12_attendees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form12_id');
            $table->foreign('form12_id')->references('id')->on('form12')->onDelete('cascade');

            $table->string('full_name')->nullable();
            $table->string('designation')->nullable();

            // school & department reference
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('programs_id')->nullable()->constrained('programs')->nullOnDelete();

            $table->timestamps();
        });

        // New items
        Schema::create('form12_new_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form12_id');
            $table->foreign('form12_id')->references('id')->on('form12')->onDelete('cascade');

            $table->text('topic')->nullable();
            $table->text('discussion')->nullable();
            $table->text('resolution')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form12_new_items');
        Schema::dropIfExists('form12_attendees');
        Schema::dropIfExists('form12');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form8', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            
            $table->string('proposed_title')->nullable();
            $table->text('introduction')->nullable();
            $table->text('method')->nullable();
            $table->text('findings_discussion')->nullable();
            $table->text('implication_intervention')->nullable();

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

        Schema::create('form8_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form8_id');
            $table->foreign('form8_id')->references('id')->on('form8')->onDelete('cascade');
            $table->text('reference'); // each reference (can be long text like citation)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form8_references');
        Schema::dropIfExists('form8');
    }
};

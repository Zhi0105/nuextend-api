<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form10', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $table->text('discussion')->nullable();

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

        Schema::create('form10_oaopb', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('form10_id');
            $table->foreignId('form10_id')->constrained('form10')->onDelete('cascade');

            $table->text('objectives')->nullable();
            $table->text('activities')->nullable();
            $table->text('outputs')->nullable();
            $table->text('personnel')->nullable();
            $table->decimal('budget', 12, 2)->nullable(); // number with decimal support

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form10_oaopb');
        Schema::dropIfExists('form10');
    }
};

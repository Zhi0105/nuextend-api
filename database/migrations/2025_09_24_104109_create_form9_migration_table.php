<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form9', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');

            $table->text('findings_discussion')->nullable();
            $table->text('conclusion_recommendations')->nullable();

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

        Schema::create('form9_logic_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form9_id');
            $table->foreignId('form9_id')->constrained('form9')->onDelete('cascade');

            $table->text('objectives')->nullable();
            $table->text('inputs')->nullable();
            $table->text('activities')->nullable();
            $table->text('outputs')->nullable();
            $table->text('outcomes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form9_logic_models');
        Schema::dropIfExists('form9');
    }
};
